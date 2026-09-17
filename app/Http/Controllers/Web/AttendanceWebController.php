<?php

namespace App\Http\Controllers\Web;

use App\Domain\Attendance\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\WorkSchedule;
use App\Models\WorkShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceWebController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    /**
     * Attendance Dashboard: Today's statistics and recent attendance logs.
     */
    public function index(Request $request): View
    {
        $today = Carbon::today()->toDateString();

        $totalEmployees = Employee::where('status', 'active')->count();

        $todayRecords = AttendanceRecord::with(['employee.department', 'employee.position', 'workSchedule.shift'])
            ->whereDate('attendance_date', $today)
            ->get();

        $presentCount = $todayRecords->whereIn('status', ['present', 'late'])->count();
        $lateCount    = $todayRecords->where('status', 'late')->count();
        $leaveCount   = $todayRecords->whereIn('status', ['leave', 'permission'])->count();
        $absentCount  = max(0, $totalEmployees - $presentCount - $leaveCount);

        // Filterable query for table
        $query = AttendanceRecord::with(['employee.department', 'employee.position', 'workSchedule.shift'])
            ->whereDate('attendance_date', $request->input('date', $today));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('employee_number', 'ilike', "%{$search}%");
            });
        }

        $records = $query->orderByDesc('check_in_at')->paginate(20)->withQueryString();

        return view('attendance.index', compact(
            'totalEmployees',
            'presentCount',
            'lateCount',
            'leaveCount',
            'absentCount',
            'records',
            'today'
        ));
    }

    /**
     * Monthly Calendar View per employee.
     */
    public function calendar(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $selectedEmployeeId = $request->input('employee_id', $employees->first()?->id);
        $selectedEmployee = $employees->firstWhere('id', $selectedEmployeeId);

        $records = [];
        if ($selectedEmployeeId) {
            $records = AttendanceRecord::where('employee_id', $selectedEmployeeId)
                ->whereBetween('attendance_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->get()
                ->keyBy(fn($r) => Carbon::parse($r->attendance_date)->toDateString());
        }

        return view('attendance.calendar', compact(
            'month',
            'startOfMonth',
            'endOfMonth',
            'employees',
            'selectedEmployee',
            'records'
        ));
    }

    /**
     * Attendance Detail Page.
     */
    public function show(string $id): View
    {
        $record = AttendanceRecord::with([
            'employee.department',
            'employee.position',
            'employee.branch',
            'workLocation',
            'workSchedule.shift',
            'events',
            'corrections.reviewer',
        ])->findOrFail($id);

        return view('attendance.show', compact('record'));
    }

    /**
     * Leave Management Page.
     */
    public function leaves(Request $request): View
    {
        $query = LeaveRequest::with(['employee.department', 'leaveType', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $leaveTypes    = LeaveType::where('is_active', true)->get();

        return view('attendance.leaves', compact('leaveRequests', 'leaveTypes'));
    }

    /**
     * Attendance Corrections Management Page.
     */
    public function corrections(Request $request): View
    {
        $query = AttendanceCorrection::with(['employee.department', 'reviewer', 'attendanceRecord']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $corrections = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('attendance.corrections', compact('corrections'));
    }

    /**
     * Work Shifts & Schedules Page.
     */
    public function shifts(): View
    {
        $shifts    = WorkShift::where('is_active', true)->orderBy('start_time')->get();
        $schedules = WorkSchedule::with(['shift', 'employee', 'team'])->orderByDesc('created_at')->paginate(15);

        return view('attendance.shifts', compact('shifts', 'schedules'));
    }
}
