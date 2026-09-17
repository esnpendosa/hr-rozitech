<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    /**
     * List leave types for the tenant.
     */
    public function index(Request $request): JsonResponse
    {
        $query = LeaveType::query();

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $leaveTypes = $query->orderBy('name')->get();

        return ApiResponse::success($leaveTypes);
    }

    /**
     * Create a new leave type.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canManageLeaves($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'code'              => ['required', 'string', 'max:50', Rule::unique('leave_types')->where('tenant_id', $user->tenant_id)],
            'default_days'      => ['nullable', 'integer', 'min:0'],
            'is_paid'           => ['nullable', 'boolean'],
            'requires_document' => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        $leaveType = LeaveType::create([
            'tenant_id'         => $user->tenant_id,
            'name'              => $validated['name'],
            'code'              => strtoupper($validated['code']),
            'default_days'      => $validated['default_days'] ?? 0,
            'is_paid'           => $validated['is_paid'] ?? true,
            'requires_document' => $validated['requires_document'] ?? false,
            'is_active'         => $validated['is_active'] ?? true,
        ]);

        return ApiResponse::created($leaveType, __('common.created_success'));
    }

    /**
     * Show leave type details.
     */
    public function show(string $id): JsonResponse
    {
        $leaveType = LeaveType::findOrFail($id);
        return ApiResponse::success($leaveType);
    }

    /**
     * Update leave type.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canManageLeaves($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $leaveType = LeaveType::findOrFail($id);

        $validated = $request->validate([
            'name'              => ['sometimes', 'required', 'string', 'max:255'],
            'code'              => ['sometimes', 'required', 'string', 'max:50', Rule::unique('leave_types')->where('tenant_id', $user->tenant_id)->ignore($leaveType->id)],
            'default_days'      => ['nullable', 'integer', 'min:0'],
            'is_paid'           => ['nullable', 'boolean'],
            'requires_document' => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $leaveType->update($validated);

        return ApiResponse::success($leaveType, __('common.updated_success'));
    }

    /**
     * Delete or deactivate leave type.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canManageLeaves($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $leaveType = LeaveType::findOrFail($id);

        // If requests exist, deactivate instead of delete
        if ($leaveType->leaveRequests()->exists()) {
            $leaveType->update(['is_active' => false]);
            return ApiResponse::success($leaveType, __('common.deactivated_success'));
        }

        $leaveType->delete();

        return ApiResponse::success(null, __('common.deleted_success'));
    }

    private function canManageLeaves(\App\Models\User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'owner', 'hr_admin'])
            || $user->hasPermissionTo('leaves.approve');
    }
}
