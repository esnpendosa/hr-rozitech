<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Domain\Attendance\Services\AttendanceService;
use App\Domain\Notification\Services\NotificationService;
use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AttendanceCorrectionController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * List attendance corrections with filters.
     *
     * Roles/Permissions:
     * - HR Admin / Owner / Super Admin: view all tenant corrections
     * - Manager / Supervisor: view team/department corrections + own
     * - Employee: view own corrections
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status'      => ['nullable', 'in:draft,submitted,approved,rejected'],
            'employee_id' => ['nullable', 'uuid'],
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $user  = auth()->user();
        $query = AttendanceCorrection::with([
            'employee:id,name,employee_number,department_id,team_id',
            'reviewer:id,name',
            'creator:id,name',
            'attendanceRecord',
        ]);

        // Scoping based on role and permissions
        if ($this->isEmployeeOnly($user)) {
            $employee = $user->employee;
            if (!$employee) {
                return ApiResponse::error(__('attendance.no_employee_record'), status: 422);
            }
            $query->where('employee_id', $employee->id);
        } elseif ($this->isManagerOrSupervisor($user)) {
            $employee = $user->employee;
            $query->where(function ($q) use ($employee) {
                if ($employee) {
                    $q->where('employee_id', $employee->id)
                      ->orWhereHas('employee', function ($eq) use ($employee) {
                          $eq->where(function ($inner) use ($employee) {
                              if ($employee->department_id) {
                                  $inner->orWhere('department_id', $employee->department_id);
                              }
                              if ($employee->team_id) {
                                  $inner->orWhere('team_id', $employee->team_id);
                              }
                          });
                      });
                }
            });
        }
        // HR Admin / Owner / Super Admin can view all

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id') && !$this->isEmployeeOnly($user)) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('correction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('correction_date', '<=', $request->date_to);
        }

        $corrections = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($corrections);
    }

    /**
     * Submit or draft an attendance correction.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'correction_date'      => ['required', 'date'],
            'requested_check_in'   => ['nullable', 'date'],
            'requested_check_out'  => ['nullable', 'date'],
            'reason'               => ['required', 'string', 'max:1000'],
            'attendance_record_id' => ['nullable', 'uuid', 'exists:attendance_records,id'],
            'status'               => ['nullable', 'in:draft,submitted'],
            'employee_id'          => ['nullable', 'uuid', 'exists:employees,id'],
        ]);

        if (empty($validated['requested_check_in']) && empty($validated['requested_check_out'])) {
            return ApiResponse::error(__('attendance.correction_time_required'), status: 422);
        }

        // Determine employee ID
        $employeeId = null;
        if (!empty($validated['employee_id'])) {
            if (!$this->canManageCorrections($user)) {
                return ApiResponse::error(__('auth.forbidden'), status: 403);
            }
            $employeeId = $validated['employee_id'];
        } else {
            $employee = $user->employee;
            if (!$employee) {
                return ApiResponse::error(__('attendance.no_employee_record'), status: 422);
            }
            $employeeId = $employee->id;
        }

        $status = $validated['status'] ?? 'submitted';

        // Prevent duplicate submitted request for the same employee and date
        if ($status === 'submitted') {
            $existingPending = AttendanceCorrection::where('employee_id', $employeeId)
                ->whereDate('correction_date', $validated['correction_date'])
                ->where('status', 'submitted')
                ->first();

            if ($existingPending) {
                return ApiResponse::error(__('attendance.correction_already_submitted'), status: 422);
            }
        }

        return DB::transaction(function () use ($request, $user, $validated, $employeeId, $status) {
            $correction = AttendanceCorrection::create([
                'tenant_id'            => $user->tenant_id,
                'employee_id'          => $employeeId,
                'attendance_record_id' => $validated['attendance_record_id'] ?? null,
                'correction_date'      => $validated['correction_date'],
                'requested_check_in'   => $validated['requested_check_in'] ?? null,
                'requested_check_out'  => $validated['requested_check_out'] ?? null,
                'reason'               => $validated['reason'],
                'status'               => $status,
                'submitted_at'         => $status === 'submitted' ? now() : null,
                'created_by'           => $user->id,
            ]);

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => $status === 'submitted' ? 'attendance.correction.submitted' : 'attendance.correction.drafted',
                'entity_type'   => 'AttendanceCorrection',
                'entity_id'     => $correction->id,
                'new_values'    => $correction->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify approvers if submitted
            if ($status === 'submitted') {
                $employee = Employee::find($employeeId);
                $this->notifyApprovers($user, $employee, $correction);
            }

            return ApiResponse::created(
                $correction->load(['employee:id,name,employee_number', 'attendanceRecord']),
                __('attendance.correction_submitted')
            );
        });
    }

    /**
     * Show attendance correction details.
     */
    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        $correction = AttendanceCorrection::with([
            'employee:id,name,employee_number,department_id,team_id',
            'reviewer:id,name',
            'creator:id,name',
            'attendanceRecord',
        ])->findOrFail($id);

        if (!$this->canViewCorrection($user, $correction)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        return ApiResponse::success($correction);
    }

    /**
     * Approve attendance correction and update attendance record.
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canManageCorrections($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $correction = AttendanceCorrection::with('employee')->findOrFail($id);

        if ($correction->status !== 'submitted') {
            return ApiResponse::error(__('attendance.correction_already_processed'), status: 422);
        }

        return DB::transaction(function () use ($request, $user, $correction, $validated) {
            $oldValues = $correction->toArray();

            $correction->update([
                'status'       => 'approved',
                'reviewed_by'  => $user->id,
                'reviewed_at'  => now(),
                'review_notes' => $validated['review_notes'] ?? null,
            ]);

            // Update or create AttendanceRecord
            $employee = $correction->employee;
            $record = null;

            if ($correction->attendance_record_id) {
                $record = AttendanceRecord::find($correction->attendance_record_id);
            }

            if (!$record && $employee) {
                $record = AttendanceRecord::where('tenant_id', $correction->tenant_id)
                    ->where('employee_id', $employee->id)
                    ->whereDate('attendance_date', $correction->correction_date)
                    ->first();
            }

            $dateObj = Carbon::parse($correction->correction_date);
            $schedule = $employee ? $this->attendanceService->findScheduleForDate($employee, $dateObj) : null;
            $shift = $schedule?->shift;

            if ($record) {
                // Update existing record
                if ($correction->requested_check_in) {
                    $record->check_in_at = $correction->requested_check_in;
                }
                if ($correction->requested_check_out) {
                    $record->check_out_at = $correction->requested_check_out;
                }

                // Recalculate status & late minutes if shift is available
                if ($shift && $record->check_in_at) {
                    $tolerance = $shift->tolerance_late_minutes ?? 0;
                    $record->late_minutes = $this->attendanceService->calculateLateMinutes($shift, Carbon::parse($record->check_in_at), $tolerance);
                    $record->status = $this->attendanceService->determineStatus($record->late_minutes);
                } elseif ($record->status === 'absent' || empty($record->status)) {
                    $record->status = 'present';
                }

                if ($shift && $record->check_out_at) {
                    $record->early_leave_minutes = $this->attendanceService->calculateEarlyLeaveMinutes($shift, Carbon::parse($record->check_out_at));
                    $record->overtime_minutes = $this->attendanceService->calculateOvertimeMinutes($shift, Carbon::parse($record->check_out_at), $shift->overtime_start_minutes ?? 0);
                }

                $record->notes = trim(($record->notes ?? '') . ' [' . __('attendance.correction_approved_note') . ']');
                $record->save();

                if ($correction->attendance_record_id !== $record->id) {
                    $correction->attendance_record_id = $record->id;
                    $correction->save();
                }
            } else {
                // Create new AttendanceRecord
                $lateMinutes = 0;
                $status = 'present';

                if ($shift && $correction->requested_check_in) {
                    $tolerance = $shift->tolerance_late_minutes ?? 0;
                    $lateMinutes = $this->attendanceService->calculateLateMinutes($shift, Carbon::parse($correction->requested_check_in), $tolerance);
                    $status = $this->attendanceService->determineStatus($lateMinutes);
                }

                $earlyLeaveMinutes = 0;
                $overtimeMinutes = 0;
                if ($shift && $correction->requested_check_out) {
                    $earlyLeaveMinutes = $this->attendanceService->calculateEarlyLeaveMinutes($shift, Carbon::parse($correction->requested_check_out));
                    $overtimeMinutes = $this->attendanceService->calculateOvertimeMinutes($shift, Carbon::parse($correction->requested_check_out), $shift->overtime_start_minutes ?? 0);
                }

                $record = AttendanceRecord::create([
                    'tenant_id'           => $correction->tenant_id,
                    'employee_id'         => $correction->employee_id,
                    'work_schedule_id'    => $schedule?->id,
                    'attendance_date'     => $correction->correction_date,
                    'check_in_at'         => $correction->requested_check_in,
                    'check_out_at'        => $correction->requested_check_out,
                    'status'              => $status,
                    'late_minutes'        => $lateMinutes,
                    'early_leave_minutes' => $earlyLeaveMinutes,
                    'overtime_minutes'    => $overtimeMinutes,
                    'source'              => 'manual',
                    'notes'               => __('attendance.correction_approved_note'),
                ]);

                $correction->attendance_record_id = $record->id;
                $correction->save();
            }

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => 'attendance.correction.approved',
                'entity_type'   => 'AttendanceCorrection',
                'entity_id'     => $correction->id,
                'old_values'    => $oldValues,
                'new_values'    => $correction->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify employee
            if ($employee && $employee->user) {
                $dateStr = Carbon::parse($correction->correction_date)->format('Y-m-d');
                $this->notificationService->send(
                    $employee->user,
                    'attendance_correction_approved',
                    __('attendance.correction_approved_title'),
                    __('attendance.correction_approved_body', ['date' => $dateStr]),
                    ['correction_id' => $correction->id]
                );
            }

            return ApiResponse::success(
                $correction->fresh(['employee:id,name,employee_number', 'reviewer:id,name', 'attendanceRecord']),
                __('attendance.correction_approved')
            );
        });
    }

    /**
     * Reject attendance correction.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canManageCorrections($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:1000'],
        ]);

        $correction = AttendanceCorrection::with('employee')->findOrFail($id);

        if ($correction->status !== 'submitted') {
            return ApiResponse::error(__('attendance.correction_already_processed'), status: 422);
        }

        return DB::transaction(function () use ($request, $user, $correction, $validated) {
            $oldValues = $correction->toArray();

            $correction->update([
                'status'       => 'rejected',
                'reviewed_by'  => $user->id,
                'reviewed_at'  => now(),
                'review_notes' => $validated['review_notes'],
            ]);

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => 'attendance.correction.rejected',
                'entity_type'   => 'AttendanceCorrection',
                'entity_id'     => $correction->id,
                'old_values'    => $oldValues,
                'new_values'    => $correction->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify employee
            $employee = $correction->employee;
            if ($employee && $employee->user) {
                $dateStr = Carbon::parse($correction->correction_date)->format('Y-m-d');
                $this->notificationService->send(
                    $employee->user,
                    'attendance_correction_rejected',
                    __('attendance.correction_rejected_title'),
                    __('attendance.correction_rejected_body', [
                        'date'   => $dateStr,
                        'reason' => $validated['review_notes'],
                    ]),
                    ['correction_id' => $correction->id]
                );
            }

            return ApiResponse::success(
                $correction->fresh(['employee:id,name,employee_number', 'reviewer:id,name']),
                __('attendance.correction_rejected')
            );
        });
    }

    // ---------------------
    // Helper Methods
    // ---------------------

    private function canManageCorrections(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'owner', 'hr_admin', 'manager'])
            || $user->hasPermissionTo('attendance.corrections.manage');
    }

    private function isEmployeeOnly(User $user): bool
    {
        return $user->hasAnyRole(['employee', 'field_worker'])
            && !$user->hasAnyRole(['manager', 'supervisor', 'hr_admin', 'owner', 'super_admin'])
            && !$user->hasPermissionTo('attendance.corrections.manage');
    }

    private function isManagerOrSupervisor(User $user): bool
    {
        return $user->hasAnyRole(['manager', 'supervisor'])
            && !$user->hasAnyRole(['hr_admin', 'owner', 'super_admin']);
    }

    private function canViewCorrection(User $user, AttendanceCorrection $correction): bool
    {
        if ($this->canManageCorrections($user) || $user->hasRole('super_admin')) {
            return true;
        }

        $userEmployee = $user->employee;
        if (!$userEmployee) {
            return false;
        }

        // Own correction
        if ($correction->employee_id === $userEmployee->id) {
            return true;
        }

        // Manager / Supervisor over team or department
        if ($this->isManagerOrSupervisor($user)) {
            $corrEmployee = $correction->employee;
            if ($corrEmployee) {
                if ($userEmployee->department_id && $corrEmployee->department_id === $userEmployee->department_id) {
                    return true;
                }
                if ($userEmployee->team_id && $corrEmployee->team_id === $userEmployee->team_id) {
                    return true;
                }
            }
        }

        return false;
    }

    private function notifyApprovers(User $actor, ?Employee $employee, AttendanceCorrection $correction): void
    {
        $approvers = User::where('tenant_id', $actor->tenant_id)
            ->where(function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->whereIn('name', ['hr_admin', 'owner', 'super_admin']);
                })->orWhereHas('permissions', function ($pq) {
                    $pq->where('name', 'attendance.corrections.manage');
                });
            })
            ->where('id', '!=', $actor->id)
            ->get();

        $empName = $employee ? $employee->name : $actor->name;
        $dateStr = Carbon::parse($correction->correction_date)->format('Y-m-d');

        $this->notificationService->sendToMany(
            $approvers,
            'attendance_correction',
            __('attendance.correction_notification_title'),
            __('attendance.correction_notification_body', ['name' => $empName, 'date' => $dateStr]),
            ['correction_id' => $correction->id]
        );
    }
}
