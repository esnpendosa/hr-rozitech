<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkScheduleController extends Controller
{
    /**
     * Display a paginated list of work schedules.
     * Gate: schedules.view
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('schedules.view');

        $query = WorkSchedule::with([
            'shift:id,name',
            'employee:id,name',
            'team:id,name',
        ]);

        // Filters
        $query->when($request->filled('employee_id'), fn ($q) => $q->where('employee_id', $request->employee_id))
              ->when($request->filled('team_id'),     fn ($q) => $q->where('team_id', $request->team_id))
              ->when($request->filled('shift_id'),    fn ($q) => $q->where('shift_id', $request->shift_id))
              ->when($request->filled('schedule_date'), fn ($q) => $q->whereDate('schedule_date', $request->schedule_date))
              ->when($request->filled('valid_from'),  fn ($q) => $q->whereDate('valid_from', '>=', $request->valid_from))
              ->when($request->filled('valid_until'), fn ($q) => $q->whereDate('valid_until', '<=', $request->valid_until));

        $schedules = $query->orderBy('valid_from', 'desc')->paginate(15);

        return ApiResponse::paginated($schedules);
    }

    /**
     * Store a newly created work schedule.
     * Gate: schedules.manage
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('schedules.manage');

        $validated = $request->validate([
            'employee_id'     => ['nullable', 'uuid', 'exists:employees,id'],
            'team_id'         => ['nullable', 'uuid', 'exists:teams,id'],
            'shift_id'        => ['required', 'uuid', 'exists:work_shifts,id'],
            'schedule_date'   => ['nullable', 'date'],
            'recurrence_type' => ['required', Rule::in(['none', 'daily', 'weekly', 'custom'])],
            'recurrence_days' => ['nullable', 'string', 'max:20'],
            'valid_from'      => ['required', 'date'],
            'valid_until'     => ['nullable', 'date', 'after_or_equal:valid_from'],
            'notes'           => ['nullable', 'string'],
        ]);

        // At least one of employee_id or team_id must be present
        if (empty($validated['employee_id']) && empty($validated['team_id'])) {
            return ApiResponse::error(
                __('attendance.employee_or_team_required'),
                ['employee_id' => [__('attendance.employee_or_team_required')]]
            );
        }

        $validated['created_by'] = auth()->id();

        $schedule = WorkSchedule::create($validated);
        $schedule->load(['shift:id,name', 'employee:id,name', 'team:id,name']);

        return ApiResponse::created($schedule, __('attendance.schedule_created'));
    }

    /**
     * Display the specified work schedule.
     * Gate: schedules.view
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('schedules.view');

        $schedule = WorkSchedule::with([
            'shift:id,name,start_time,end_time',
            'employee:id,name',
            'team:id,name',
            'creator:id,name',
        ])->find($id);

        if (!$schedule) {
            return ApiResponse::notFound(__('attendance.schedule_not_found'));
        }

        return ApiResponse::success($schedule);
    }

    /**
     * Update the specified work schedule.
     * Gate: schedules.manage
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('schedules.manage');

        $schedule = WorkSchedule::find($id);

        if (!$schedule) {
            return ApiResponse::notFound(__('attendance.schedule_not_found'));
        }

        $validated = $request->validate([
            'employee_id'     => ['nullable', 'uuid', 'exists:employees,id'],
            'team_id'         => ['nullable', 'uuid', 'exists:teams,id'],
            'shift_id'        => ['sometimes', 'required', 'uuid', 'exists:work_shifts,id'],
            'schedule_date'   => ['nullable', 'date'],
            'recurrence_type' => ['sometimes', 'required', Rule::in(['none', 'daily', 'weekly', 'custom'])],
            'recurrence_days' => ['nullable', 'string', 'max:20'],
            'valid_from'      => ['sometimes', 'required', 'date'],
            'valid_until'     => ['nullable', 'date', 'after_or_equal:valid_from'],
            'notes'           => ['nullable', 'string'],
        ]);

        // If either employee_id or team_id is being explicitly set, ensure at least one is present
        $newEmployeeId = array_key_exists('employee_id', $validated)
            ? $validated['employee_id']
            : $schedule->employee_id;
        $newTeamId = array_key_exists('team_id', $validated)
            ? $validated['team_id']
            : $schedule->team_id;

        if (empty($newEmployeeId) && empty($newTeamId)) {
            return ApiResponse::error(
                __('attendance.employee_or_team_required'),
                ['employee_id' => [__('attendance.employee_or_team_required')]]
            );
        }

        $schedule->update($validated);
        $schedule->load(['shift:id,name', 'employee:id,name', 'team:id,name']);

        return ApiResponse::success($schedule, __('attendance.schedule_updated'));
    }

    /**
     * Soft-delete the specified work schedule.
     * Gate: schedules.manage
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('schedules.manage');

        $schedule = WorkSchedule::find($id);

        if (!$schedule) {
            return ApiResponse::notFound(__('attendance.schedule_not_found'));
        }

        $schedule->delete();

        return ApiResponse::success(null, __('attendance.schedule_deleted'));
    }
}
