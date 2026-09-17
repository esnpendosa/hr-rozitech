<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Domain\Notification\Services\NotificationService;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OvertimeController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * List overtime requests with filters and role-based scoping.
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
        $query = OvertimeRequest::with([
            'employee:id,name,employee_number,department_id,team_id',
            'reviewer:id,name',
        ]);

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id') && !$this->isEmployeeOnly($user)) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('overtime_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('overtime_date', '<=', $request->date_to);
        }

        $overtime = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($overtime);
    }

    /**
     * Submit overtime request.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'overtime_date' => ['required', 'date'],
            'start_time'    => ['required', 'string'],
            'end_time'      => ['required', 'string'],
            'reason'        => ['required', 'string', 'max:1000'],
            'employee_id'   => ['nullable', 'uuid', 'exists:employees,id'],
            'status'        => ['nullable', 'in:draft,submitted'],
        ]);

        $employeeId = null;
        if (!empty($validated['employee_id'])) {
            if (!$this->canApproveOvertime($user)) {
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

        // Calculate total minutes
        $dateStr   = $validated['overtime_date'];
        $startDT   = Carbon::parse("{$dateStr} {$validated['start_time']}");
        $endDT     = Carbon::parse("{$dateStr} {$validated['end_time']}");
        if ($endDT->lt($startDT)) {
            $endDT->addDay(); // Overnight overtime
        }
        $totalMinutes = $startDT->diffInMinutes($endDT);

        $status = $validated['status'] ?? 'submitted';

        return DB::transaction(function () use ($request, $user, $validated, $employeeId, $totalMinutes, $status) {
            $overtime = OvertimeRequest::create([
                'tenant_id'     => $user->tenant_id,
                'employee_id'   => $employeeId,
                'overtime_date' => $validated['overtime_date'],
                'start_time'    => $validated['start_time'],
                'end_time'      => $validated['end_time'],
                'total_minutes' => $totalMinutes,
                'reason'        => $validated['reason'],
                'status'        => $status,
                'submitted_at'  => $status === 'submitted' ? now() : null,
            ]);

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => $status === 'submitted' ? 'overtime.submitted' : 'overtime.drafted',
                'entity_type'   => 'OvertimeRequest',
                'entity_id'     => $overtime->id,
                'new_values'    => $overtime->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify approvers if submitted
            if ($status === 'submitted') {
                $employee = Employee::find($employeeId);
                $this->notifyApprovers($user, $employee, $overtime);
            }

            return ApiResponse::created(
                $overtime->load('employee:id,name,employee_number'),
                __('attendance.overtime_submitted')
            );
        });
    }

    /**
     * Show overtime request detail.
     */
    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        $overtime = OvertimeRequest::with([
            'employee:id,name,employee_number,department_id,team_id',
            'reviewer:id,name',
        ])->findOrFail($id);

        if (!$this->canViewOvertime($user, $overtime)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        return ApiResponse::success($overtime);
    }

    /**
     * Approve overtime request and update attendance records.
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canApproveOvertime($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $overtime = OvertimeRequest::with('employee.user')->findOrFail($id);

        if ($overtime->status !== 'submitted') {
            return ApiResponse::error(__('attendance.overtime_already_processed'), status: 422);
        }

        return DB::transaction(function () use ($request, $user, $overtime, $validated) {
            $oldValues = $overtime->toArray();

            $overtime->update([
                'status'       => 'approved',
                'reviewed_by'  => $user->id,
                'reviewed_at'  => now(),
                'review_notes' => $validated['review_notes'] ?? null,
            ]);

            // Update AttendanceRecord if present
            $record = AttendanceRecord::where('tenant_id', $overtime->tenant_id)
                ->where('employee_id', $overtime->employee_id)
                ->whereDate('attendance_date', $overtime->overtime_date)
                ->first();

            if ($record) {
                $record->overtime_minutes = ($record->overtime_minutes ?? 0) + $overtime->total_minutes;
                $record->notes = trim(($record->notes ?? '') . " [Overtime: {$overtime->total_minutes}m]");
                $record->save();
            }

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => 'overtime.approved',
                'entity_type'   => 'OvertimeRequest',
                'entity_id'     => $overtime->id,
                'old_values'    => $oldValues,
                'new_values'    => $overtime->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify employee
            $employee = $overtime->employee;
            if ($employee && $employee->user) {
                $dateStr = Carbon::parse($overtime->overtime_date)->format('Y-m-d');
                $this->notificationService->send(
                    $employee->user,
                    'overtime_approved',
                    __('attendance.overtime_approved_title'),
                    __('attendance.overtime_approved_body', [
                        'date'    => $dateStr,
                        'minutes' => $overtime->total_minutes,
                    ]),
                    ['overtime_id' => $overtime->id]
                );
            }

            return ApiResponse::success(
                $overtime->fresh(['employee:id,name,employee_number', 'reviewer:id,name']),
                __('attendance.overtime_approved')
            );
        });
    }

    /**
     * Reject overtime request.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canApproveOvertime($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:1000'],
        ]);

        $overtime = OvertimeRequest::with('employee.user')->findOrFail($id);

        if ($overtime->status !== 'submitted') {
            return ApiResponse::error(__('attendance.overtime_already_processed'), status: 422);
        }

        return DB::transaction(function () use ($request, $user, $overtime, $validated) {
            $oldValues = $overtime->toArray();

            $overtime->update([
                'status'       => 'rejected',
                'reviewed_by'  => $user->id,
                'reviewed_at'  => now(),
                'review_notes' => $validated['review_notes'],
            ]);

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => 'overtime.rejected',
                'entity_type'   => 'OvertimeRequest',
                'entity_id'     => $overtime->id,
                'old_values'    => $oldValues,
                'new_values'    => $overtime->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify employee
            $employee = $overtime->employee;
            if ($employee && $employee->user) {
                $dateStr = Carbon::parse($overtime->overtime_date)->format('Y-m-d');
                $this->notificationService->send(
                    $employee->user,
                    'overtime_rejected',
                    __('attendance.overtime_rejected_title'),
                    __('attendance.overtime_rejected_body', [
                        'date'   => $dateStr,
                        'reason' => $validated['review_notes'],
                    ]),
                    ['overtime_id' => $overtime->id]
                );
            }

            return ApiResponse::success(
                $overtime->fresh(['employee:id,name,employee_number', 'reviewer:id,name']),
                __('attendance.overtime_rejected')
            );
        });
    }

    // ---------------------
    // Helper Methods
    // ---------------------

    private function canApproveOvertime(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'owner', 'hr_admin', 'manager'])
            || $user->hasPermissionTo('overtime.approve');
    }

    private function isEmployeeOnly(User $user): bool
    {
        return $user->hasAnyRole(['employee', 'field_worker'])
            && !$user->hasAnyRole(['manager', 'supervisor', 'hr_admin', 'owner', 'super_admin'])
            && !$user->hasPermissionTo('overtime.approve');
    }

    private function isManagerOrSupervisor(User $user): bool
    {
        return $user->hasAnyRole(['manager', 'supervisor'])
            && !$user->hasAnyRole(['hr_admin', 'owner', 'super_admin']);
    }

    private function canViewOvertime(User $user, OvertimeRequest $overtime): bool
    {
        if ($this->canApproveOvertime($user) || $user->hasRole('super_admin')) {
            return true;
        }

        $userEmployee = $user->employee;
        if (!$userEmployee) {
            return false;
        }

        if ($overtime->employee_id === $userEmployee->id) {
            return true;
        }

        if ($this->isManagerOrSupervisor($user)) {
            $reqEmployee = $overtime->employee;
            if ($reqEmployee) {
                if ($userEmployee->department_id && $reqEmployee->department_id === $userEmployee->department_id) {
                    return true;
                }
                if ($userEmployee->team_id && $reqEmployee->team_id === $userEmployee->team_id) {
                    return true;
                }
            }
        }

        return false;
    }

    private function notifyApprovers(User $actor, ?Employee $employee, OvertimeRequest $overtime): void
    {
        $approvers = User::where('tenant_id', $actor->tenant_id)
            ->where(function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->whereIn('name', ['hr_admin', 'owner', 'super_admin']);
                })->orWhereHas('permissions', function ($pq) {
                    $pq->where('name', 'overtime.approve');
                });
            })
            ->where('id', '!=', $actor->id)
            ->get();

        $empName = $employee ? $employee->name : $actor->name;
        $dateStr = Carbon::parse($overtime->overtime_date)->format('Y-m-d');

        $this->notificationService->sendToMany(
            $approvers,
            'overtime_request',
            __('attendance.overtime_notification_title'),
            __('attendance.overtime_notification_body', [
                'name'    => $empName,
                'date'    => $dateStr,
                'minutes' => $overtime->total_minutes,
            ]),
            ['overtime_id' => $overtime->id]
        );
    }
}
