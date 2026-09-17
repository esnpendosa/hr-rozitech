<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendanceRecordController extends Controller
{
    /**
     * List attendance records with filters.
     *
     * Gate: attendance.records.view
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('attendance.records.view');

        $request->validate([
            'employee_id' => ['nullable', 'uuid'],
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date', 'after_or_equal:date_from'],
            'status'      => ['nullable', 'in:present,absent,late,permission,leave,holiday,off'],
            'month'       => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'source'      => ['nullable', 'in:mobile,fingerprint,manual,system'],
        ]);

        $user  = auth()->user();
        $query = AttendanceRecord::with('employee:id,name,employee_number');

        // Manager/supervisor: scope to their own department/team employees
        if ($this->isManagerOrSupervisor($user)) {
            $employee = $user->employee;
            if ($employee) {
                $query->whereHas('employee', function ($q) use ($employee) {
                    $q->where(function ($inner) use ($employee) {
                        if ($employee->department_id) {
                            $inner->orWhere('department_id', $employee->department_id);
                        }
                        if ($employee->team_id) {
                            $inner->orWhere('team_id', $employee->team_id);
                        }
                    });
                });
            }
        }
        // HR admin / owner: no extra scope — all employees visible

        // Filter: specific employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter: month (YYYY-MM)
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('attendance_date', $year)
                  ->whereMonth('attendance_date', $month);
        } else {
            // Filter: date range
            if ($request->filled('date_from')) {
                $query->whereDate('attendance_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('attendance_date', '<=', $request->date_to);
            }
        }

        // Filter: status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $records = $query->orderByDesc('attendance_date')->paginate(30);

        return ApiResponse::paginated($records);
    }

    /**
     * Show a single attendance record with full relations.
     *
     * Gate: attendance.records.view
     */
    public function show(string $id): JsonResponse
    {
        Gate::authorize('attendance.records.view');

        $record = AttendanceRecord::with([
            'employee',
            'workSchedule.shift',
            'workLocation',
            'events',
        ])->find($id);

        if (!$record) {
            return ApiResponse::notFound(__('attendance.record_not_found'));
        }

        return ApiResponse::success($record);
    }

    /**
     * Attendance summary/recap by period.
     *
     * Gate: attendance.records.view
     */
    public function summary(Request $request): JsonResponse
    {
        Gate::authorize('attendance.records.view');

        $request->validate([
            'employee_id' => ['nullable', 'uuid'],
            'month'       => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'period'      => ['nullable', 'in:daily,weekly,monthly'],
        ]);

        [$year, $month] = explode('-', $request->month);
        $period = $request->input('period', 'monthly');

        $startDate = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $query = AttendanceRecord::whereYear('attendance_date', $year)
                                 ->whereMonth('attendance_date', $month);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $records = $query->get();

        // Count working days in period (Mon–Fri, excluding weekends)
        // For a more robust solution, holiday tables would be consulted;
        // here we use business days as a reasonable default.
        $totalWorkingDays = $this->countWorkingDays($startDate, $endDate, $period);

        $present          = $records->whereIn('status', ['present'])->count();
        $late             = $records->where('status', 'late')->count();
        $absent           = $records->where('status', 'absent')->count();
        $permission       = $records->where('status', 'permission')->count();
        $leave            = $records->where('status', 'leave')->count();
        $totalLateMinutes = (int) $records->sum('late_minutes');
        $totalOvertime    = (int) $records->sum('overtime_minutes');

        $attendedDays    = $present + $late;
        $attendanceRate  = $totalWorkingDays > 0
            ? round(($attendedDays / $totalWorkingDays) * 100, 2)
            : 0.0;

        return ApiResponse::success([
            'total_days'            => $totalWorkingDays,
            'present'               => $present,
            'late'                  => $late,
            'absent'                => $absent,
            'permission'            => $permission,
            'leave'                 => $leave,
            'total_late_minutes'    => $totalLateMinutes,
            'total_overtime_minutes'=> $totalOvertime,
            'attendance_rate'       => $attendanceRate,
        ], __('attendance.summary'));
    }

    /**
     * Calendar view — all days in a month with status per day.
     *
     * Gate: attendance.records.view
     */
    public function calendar(Request $request): JsonResponse
    {
        Gate::authorize('attendance.records.view');

        $request->validate([
            'employee_id' => ['required', 'uuid'],
            'month'       => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ]);

        [$year, $month] = explode('-', $request->month);

        $startDate = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Fetch existing records for this employee and month
        $records = AttendanceRecord::where('employee_id', $request->employee_id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get()
            ->keyBy(fn ($r) => $r->attendance_date->toDateString());

        // Build calendar array covering every day of the month
        $calendar = [];
        $period   = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $record  = $records->get($dateStr);

            if ($record) {
                $calendar[] = [
                    'date'             => $dateStr,
                    'status'           => $record->status,
                    'check_in_at'      => $record->check_in_at?->toIso8601String(),
                    'check_out_at'     => $record->check_out_at?->toIso8601String(),
                    'late_minutes'     => $record->late_minutes ?? 0,
                    'overtime_minutes' => $record->overtime_minutes ?? 0,
                ];
            } else {
                // Days without a record are treated as absent
                $calendar[] = [
                    'date'             => $dateStr,
                    'status'           => 'absent',
                    'check_in_at'      => null,
                    'check_out_at'     => null,
                    'late_minutes'     => 0,
                    'overtime_minutes' => 0,
                ];
            }
        }

        return ApiResponse::success($calendar, __('attendance.calendar'));
    }

    // ---------------------
    // Private helpers
    // ---------------------

    /**
     * Determine if the user has a manager/supervisor role (not HR admin or owner).
     */
    private function isManagerOrSupervisor(\App\Models\User $user): bool
    {
        return $user->hasAnyRole(['manager', 'supervisor'])
            && !$user->hasAnyRole(['hr_admin', 'owner', 'super_admin'])
            && !$user->hasAnyPermission(['manage attendance']);
    }

    /**
     * Count working days (Mon–Fri) in a date range, adjusted to period granularity.
     * For 'monthly' and 'daily'/'weekly', we use the full month working days as total.
     */
    private function countWorkingDays(Carbon $start, Carbon $end, string $period): int
    {
        $count   = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // Mon=1 ... Fri=5 (isWeekday)
            if ($current->isWeekday()) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }
}
