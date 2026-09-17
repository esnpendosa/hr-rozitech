<?php

namespace App\Http\Controllers\Api\V1\Target;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Target;
use App\Models\TargetAssignment;
use App\Models\TargetProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class TargetController
 *
 * Mengelola target kinerja (Target CRUD, pembaruan progres aktual, kalkulasi risiko otomatis).
 */
class TargetController extends Controller
{
    /**
     * Menampilkan daftar target kinerja dengan filter periode dan risiko.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $targets = Target::where('tenant_id', $tenantId)
            ->with(['project', 'assignments.employee.user', 'assignments.team'])
            ->when($request->filled('period'), fn($q) => $q->where('period', $request->period))
            ->when($request->filled('risk_level'), fn($q) => $q->where('risk_level', $request->risk_level))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($targets);
    }

    /**
     * Membuat target kinerja baru.
     */
    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $validated = $request->validate([
            'project_id'    => 'nullable|uuid|exists:projects,id',
            'name'          => 'required|string|max:255',
            'metric'        => 'required|string|max:100',
            'target_value'  => 'required|numeric|min:0.01',
            'unit'          => 'nullable|string|max:50',
            'period'        => 'required|in:daily,weekly,monthly,quarterly,annual,custom',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'notes'         => 'nullable|string',
            'assignee_ids'  => 'nullable|array',
            'assignee_ids.*'=> 'uuid|exists:employees,id',
        ]);

        $target = DB::transaction(function () use ($validated, $tenantId, $userId) {
            $target = Target::create([
                'tenant_id'    => $tenantId,
                'project_id'   => $validated['project_id'] ?? null,
                'name'         => $validated['name'],
                'metric'       => $validated['metric'],
                'target_value' => $validated['target_value'],
                'unit'         => $validated['unit'] ?? null,
                'period'       => $validated['period'],
                'start_date'   => $validated['start_date'],
                'end_date'     => $validated['end_date'],
                'notes'        => $validated['notes'] ?? null,
                'created_by'   => $userId,
            ]);

            if (!empty($validated['assignee_ids'])) {
                foreach ($validated['assignee_ids'] as $empId) {
                    TargetAssignment::create([
                        'tenant_id'     => $tenantId,
                        'target_id'     => $target->id,
                        'assignee_type' => 'employee',
                        'employee_id'   => $empId,
                        'created_at'    => now(),
                    ]);
                }
            }

            $target->recalculateMetrics();

            return $target;
        });

        $target->load(['assignments.employee.user']);

        return ApiResponse::created($target, 'Target kinerja berhasil dibuat');
    }

    /**
     * Menampilkan detail target kinerja beserta breakdown kalkulasi risiko.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $target = Target::where('tenant_id', $tenantId)
            ->with([
                'project',
                'assignments.employee.user',
                'assignments.team',
                'progressRecords.recorder',
            ])
            ->findOrFail($id);

        return ApiResponse::success($target, 'Detail target kinerja berhasil diambil');
    }

    /**
     * Menginputkan progres capaian aktual terbaru ke target.
     */
    public function recordProgress(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $target = Target::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'value'       => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string|max:1000',
            'recorded_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($target, $validated, $userId, $tenantId) {
            TargetProgress::create([
                'tenant_id'   => $tenantId,
                'target_id'   => $target->id,
                'recorded_by' => $userId,
                'value'       => $validated['value'],
                'notes'       => $validated['notes'] ?? null,
                'recorded_at' => $validated['recorded_at'] ?? now(),
                'created_at'  => now(),
            ]);

            $target->increment('actual_value', $validated['value']);
            $target->fresh()->recalculateMetrics();
        });

        return ApiResponse::success($target->fresh(), 'Progres target berhasil dicatat');
    }

    /**
     * Memperbarui informasi target.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $target = Target::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'metric'       => 'sometimes|string|max:100',
            'target_value' => 'sometimes|numeric|min:0.01',
            'unit'         => 'nullable|string|max:50',
            'start_date'   => 'sometimes|date',
            'end_date'     => 'sometimes|date',
            'notes'        => 'nullable|string',
        ]);

        $target->update($validated);
        $target->recalculateMetrics();

        return ApiResponse::success($target, 'Target berhasil diperbarui');
    }

    /**
     * Menghapus target (soft delete).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $target = Target::where('tenant_id', $tenantId)->findOrFail($id);
        $target->delete();

        return ApiResponse::success(null, 'Target berhasil dihapus');
    }
}
