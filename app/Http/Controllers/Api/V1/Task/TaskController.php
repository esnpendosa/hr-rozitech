<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Application\Services\ApiResponse;
use App\Domain\Notification\Services\NotificationService;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class TaskController
 *
 * Mengelola siklus penuh tugas kerja:
 * - Listing filterable & searchable (status, priority, project, assignee)
 * - Pembuatan tugas dengan multi-assignee dan notifikasi FCM
 * - Perubahan status tugas dengan validasi alur dan histori
 */
class TaskController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Mengambil daftar tugas dengan filter dinamis.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $query = Task::where('tenant_id', $tenantId)
            ->with(['project', 'assignees.employee.user', 'creator'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->priority))
            ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = "%{$request->search}%";
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'ilike', $term)
                        ->orWhere('description', 'ilike', $term);
                });
            })
            ->when($request->filled('employee_id'), function ($q) use ($request) {
                $q->whereHas('assignees', fn($a) => $a->where('employee_id', $request->employee_id));
            });

        $tasks = $query->latest()->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($tasks);
    }

    /**
     * Membuat tugas baru dan menetapkan anggota tim pelaksana.
     */
    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $validated = $request->validate([
            'project_id'       => 'nullable|uuid|exists:projects,id',
            'customer_id'      => 'nullable|uuid',
            'title'            => 'required|string|max:500',
            'description'      => 'nullable|string',
            'priority'         => 'required|in:low,medium,high,critical',
            'start_at'         => 'nullable|date',
            'due_at'           => 'nullable|date|after_or_equal:start_at',
            'target_value'     => 'nullable|numeric|min:0',
            'target_unit'      => 'nullable|string|max:50',
            'assignee_ids'     => 'nullable|array',
            'assignee_ids.*'   => 'uuid|exists:employees,id',
        ]);

        $task = DB::transaction(function () use ($validated, $tenantId, $userId) {
            $task = Task::create([
                'tenant_id'    => $tenantId,
                'project_id'   => $validated['project_id'] ?? null,
                'customer_id'  => $validated['customer_id'] ?? null,
                'assigned_by'  => $userId,
                'created_by'   => $userId,
                'priority'     => $validated['priority'],
                'title'        => $validated['title'],
                'description'  => $validated['description'] ?? null,
                'status'       => 'assigned',
                'start_at'     => $validated['start_at'] ?? now(),
                'due_at'       => $validated['due_at'] ?? null,
                'target_value' => $validated['target_value'] ?? null,
                'target_unit'  => $validated['target_unit'] ?? null,
            ]);

            // Hubungkan pegawai pelaksana
            if (!empty($validated['assignee_ids'])) {
                foreach ($validated['assignee_ids'] as $empId) {
                    TaskAssignee::create([
                        'tenant_id'   => $tenantId,
                        'task_id'     => $task->id,
                        'employee_id' => $empId,
                        'assigned_at' => now(),
                        'created_at'  => now(),
                    ]);
                }
            }

            // Catat status awal
            TaskStatusHistory::create([
                'tenant_id'   => $tenantId,
                'task_id'     => $task->id,
                'from_status' => null,
                'to_status'   => 'assigned',
                'changed_by'  => $userId,
                'notes'       => 'Tugas baru dibuat dan ditugaskan.',
                'created_at'  => now(),
            ]);

            return $task;
        });

        // Notifikasi ke seluruh penerima tugas
        if (!empty($validated['assignee_ids'])) {
            $employees = Employee::whereIn('id', $validated['assignee_ids'])->with('user')->get();
            foreach ($employees as $employee) {
                if ($employee->user) {
                    $this->notificationService->send(
                        recipient: $employee->user,
                        type: 'task',
                        title: 'Tugas Baru Diberikan',
                        body: "Anda telah ditugaskan pada pekerjaan: '{$task->title}'",
                        data: ['task_id' => $task->id]
                    );
                }
            }
        }

        $task->load(['project', 'assignees.employee.user', 'creator']);

        return ApiResponse::created($task, 'Tugas berhasil dibuat');
    }

    /**
     * Menampilkan informasi detail tugas secara komprehensif.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $task = Task::where('tenant_id', $tenantId)
            ->with([
                'project',
                'assignees.employee.user',
                'checklists',
                'evidences.uploader',
                'comments.user',
                'statusHistories.changer',
            ])
            ->findOrFail($id);

        return ApiResponse::success($task, 'Detail tugas berhasil diambil');
    }

    /**
     * Memperbarui parameter tugas (judul, deskripsi, prioritas, deadline).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $task = Task::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'project_id'       => 'nullable|uuid|exists:projects,id',
            'title'            => 'sometimes|string|max:500',
            'description'      => 'nullable|string',
            'priority'         => 'sometimes|in:low,medium,high,critical',
            'start_at'         => 'nullable|date',
            'due_at'           => 'nullable|date',
            'target_value'     => 'nullable|numeric|min:0',
            'target_unit'      => 'nullable|string|max:50',
            'progress_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['updated_by'] = $request->user()->id;

        $task->update($validated);

        return ApiResponse::success($task, 'Tugas berhasil diperbarui');
    }

    /**
     * Memperbarui status tugas sesuai alur siklus hidupnya.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $task = Task::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:draft,assigned,accepted,in_progress,waiting,review,completed,rejected,cancelled,overdue',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $oldStatus = $task->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return ApiResponse::success($task, 'Status tidak berubah');
        }

        DB::transaction(function () use ($task, $oldStatus, $newStatus, $userId, $validated, $tenantId) {
            $updateData = [
                'status'     => $newStatus,
                'updated_by' => $userId,
            ];

            if ($newStatus === 'completed') {
                $updateData['completed_at'] = now();
                $updateData['progress_percent'] = 100;
            }

            $task->update($updateData);

            TaskStatusHistory::create([
                'tenant_id'   => $tenantId,
                'task_id'     => $task->id,
                'from_status' => $oldStatus,
                'to_status'   => $newStatus,
                'changed_by'  => $userId,
                'notes'       => $validated['notes'] ?? null,
                'created_at'  => now(),
            ]);

            // Sinkronkan progres ke Project induk jika terhubung
            if ($task->project_id) {
                $task->project?->recalculateProgress();
            }
        });

        // Notifikasi ke pembuat tugas bila status berubah
        if ($task->creator && $task->creator->id !== $userId) {
            $this->notificationService->send(
                recipient: $task->creator,
                type: 'task',
                title: 'Status Tugas Berubah',
                body: "Status tugas '{$task->title}' kini menjadi: " . strtoupper($newStatus),
                data: ['task_id' => $task->id, 'status' => $newStatus]
            );
        }

        return ApiResponse::success($task, 'Status tugas berhasil diperbarui');
    }

    /**
     * Menghapus tugas (soft delete).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $task = Task::where('tenant_id', $tenantId)->findOrFail($id);
        $task->delete();

        return ApiResponse::success(null, 'Tugas berhasil dihapus');
    }
}
