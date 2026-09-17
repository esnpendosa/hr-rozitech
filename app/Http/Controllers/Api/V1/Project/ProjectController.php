<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ProjectController
 *
 * Mengelola siklus hidup proyek (CRUD, penetapan anggota tim, dan perincian tugas).
 */
class ProjectController extends Controller
{
    /**
     * Menampilkan daftar proyek yang terdaftar pada tenant.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $projects = Project::where('tenant_id', $tenantId)
            ->with(['manager', 'creator'])
            ->withCount(['tasks', 'members'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($projects);
    }

    /**
     * Membuat entitas proyek baru.
     */
    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50',
            'description'     => 'nullable|string',
            'manager_user_id' => 'nullable|uuid|exists:users,id',
            'start_date'      => 'nullable|date',
            'deadline'        => 'nullable|date|after_or_equal:start_date',
            'status'          => 'required|in:planning,active,on_hold,completed,cancelled',
            'budget'          => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['created_by'] = $userId;

        $project = Project::create($validated);
        $project->load(['manager', 'creator']);

        return ApiResponse::created($project, 'Proyek berhasil dibuat');
    }

    /**
     * Mengambil detail lengkap proyek beserta tugas dan anggota tim.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $project = Project::where('tenant_id', $tenantId)
            ->with([
                'manager',
                'creator',
                'members.employee.user',
                'tasks' => fn($q) => $q->latest()->limit(10),
            ])
            ->findOrFail($id);

        return ApiResponse::success($project, 'Detail proyek berhasil diambil');
    }

    /**
     * Memperbarui informasi proyek.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $project = Project::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'code'            => 'nullable|string|max:50',
            'description'     => 'nullable|string',
            'manager_user_id' => 'nullable|uuid|exists:users,id',
            'start_date'      => 'nullable|date',
            'deadline'        => 'nullable|date',
            'status'          => 'sometimes|in:planning,active,on_hold,completed,cancelled',
            'budget'          => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $project->update($validated);

        return ApiResponse::success($project, 'Proyek berhasil diperbarui');
    }

    /**
     * Menghapus proyek (soft delete).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $project = Project::where('tenant_id', $tenantId)->findOrFail($id);
        $project->delete();

        return ApiResponse::success(null, 'Proyek berhasil dihapus');
    }

    /**
     * Menambahkan anggota tim pelaksana ke proyek.
     */
    public function addMember(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $project = Project::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|uuid|exists:employees,id',
            'role'        => 'nullable|string|max:100', // contoh: 'lead', 'developer', 'qa'
        ]);

        // Verifikasi kepemilikan tenant untuk employee
        Employee::where('tenant_id', $tenantId)->findOrFail($validated['employee_id']);

        $member = ProjectMember::updateOrCreate(
            [
                'tenant_id'   => $tenantId,
                'project_id'  => $project->id,
                'employee_id' => $validated['employee_id'],
            ],
            [
                'role'      => $validated['role'] ?? 'member',
                'joined_at' => now(),
            ]
        );

        $member->load('employee.user');

        return ApiResponse::created($member, 'Anggota tim proyek berhasil ditambahkan');
    }

    /**
     * Menghapus anggota dari proyek.
     */
    public function removeMember(Request $request, string $id, string $memberId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $project = Project::where('tenant_id', $tenantId)->findOrFail($id);

        $member = ProjectMember::where('tenant_id', $tenantId)
            ->where('project_id', $project->id)
            ->findOrFail($memberId);

        $member->delete();

        return ApiResponse::success(null, 'Anggota berhasil dikeluarkan dari proyek');
    }

    /**
     * Mengambil seluruh tugas yang terhubung ke proyek ini.
     */
    public function getTasks(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $project = Project::where('tenant_id', $tenantId)->findOrFail($id);

        $tasks = $project->tasks()
            ->with(['assignees.employee.user'])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return ApiResponse::paginated($tasks);
    }
}
