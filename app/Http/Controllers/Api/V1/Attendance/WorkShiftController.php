<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\WorkShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkShiftController extends Controller
{
    /**
     * Display a paginated list of work shifts.
     * Gate: shifts.view
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('shifts.view');

        $query = WorkShift::query();

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                  ->orWhere('code', 'ilike', '%' . $search . '%');
            });
        }

        // Filter by active status
        $query->when(
            $request->filled('is_active'),
            fn ($q) => $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
        );

        $shifts = $query->orderBy('name')->paginate(15);

        return ApiResponse::paginated($shifts);
    }

    /**
     * Store a newly created work shift.
     * Gate: shifts.manage
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('shifts.manage');

        $validated = $request->validate([
            'name'                          => ['required', 'string', 'max:255'],
            'code'                          => ['nullable', 'string', 'max:50'],
            'start_time'                    => ['required', 'date_format:H:i'],
            'end_time'                      => ['required', 'date_format:H:i'],
            'tolerance_late_minutes'        => ['nullable', 'integer', 'min:0'],
            'tolerance_early_leave_minutes' => ['nullable', 'integer', 'min:0'],
            'overtime_threshold_minutes'    => ['nullable', 'integer', 'min:0'],
            'is_overnight'                  => ['nullable', 'boolean'],
            'is_active'                     => ['nullable', 'boolean'],
        ]);

        // Defaults
        $validated['tolerance_late_minutes']        = $validated['tolerance_late_minutes'] ?? 0;
        $validated['tolerance_early_leave_minutes'] = $validated['tolerance_early_leave_minutes'] ?? 0;
        $validated['overtime_threshold_minutes']    = $validated['overtime_threshold_minutes'] ?? 0;
        $validated['is_overnight']                  = $validated['is_overnight'] ?? false;
        $validated['is_active']                     = $validated['is_active'] ?? true;

        $shift = WorkShift::create($validated);

        return ApiResponse::created($shift, __('attendance.shift_created'));
    }

    /**
     * Display the specified work shift.
     * Gate: shifts.view
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('shifts.view');

        $shift = WorkShift::find($id);

        if (!$shift) {
            return ApiResponse::notFound(__('attendance.shift_not_found'));
        }

        return ApiResponse::success($shift);
    }

    /**
     * Update the specified work shift.
     * Gate: shifts.manage
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('shifts.manage');

        $shift = WorkShift::find($id);

        if (!$shift) {
            return ApiResponse::notFound(__('attendance.shift_not_found'));
        }

        $validated = $request->validate([
            'name'                          => ['sometimes', 'required', 'string', 'max:255'],
            'code'                          => ['nullable', 'string', 'max:50'],
            'start_time'                    => ['sometimes', 'required', 'date_format:H:i'],
            'end_time'                      => ['sometimes', 'required', 'date_format:H:i'],
            'tolerance_late_minutes'        => ['nullable', 'integer', 'min:0'],
            'tolerance_early_leave_minutes' => ['nullable', 'integer', 'min:0'],
            'overtime_threshold_minutes'    => ['nullable', 'integer', 'min:0'],
            'is_overnight'                  => ['nullable', 'boolean'],
            'is_active'                     => ['nullable', 'boolean'],
        ]);

        $shift->update($validated);

        return ApiResponse::success($shift, __('attendance.shift_updated'));
    }

    /**
     * Soft-delete the specified work shift.
     * Gate: shifts.manage
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('shifts.manage');

        $shift = WorkShift::find($id);

        if (!$shift) {
            return ApiResponse::notFound(__('attendance.shift_not_found'));
        }

        $shift->delete();

        return ApiResponse::success(null, __('attendance.shift_deleted'));
    }
}
