<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Domain\Notification\Services\NotificationService;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeaveRequestController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * List leave requests with filters and role-based scoping.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status'        => ['nullable', 'in:draft,submitted,approved,rejected,cancelled'],
            'employee_id'   => ['nullable', 'uuid'],
            'leave_type_id' => ['nullable', 'uuid'],
            'date_from'     => ['nullable', 'date'],
            'date_to'       => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $user  = auth()->user();
        $query = LeaveRequest::with([
            'employee:id,name,employee_number,department_id,team_id',
            'leaveType:id,name,code,is_paid',
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

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('end_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $leaves = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($leaves);
    }

    /**
     * Submit a leave request.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'leave_type_id' => ['required', 'uuid', 'exists:leave_types,id'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'reason'        => ['nullable', 'string', 'max:1000'],
            'attachment'    => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
            'employee_id'   => ['nullable', 'uuid', 'exists:employees,id'],
            'status'        => ['nullable', 'in:draft,submitted'],
        ]);

        $employeeId = null;
        if (!empty($validated['employee_id'])) {
            if (!$this->canApproveLeaves($user)) {
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

        $leaveType = LeaveType::findOrFail($validated['leave_type_id']);

        // Check if document is required
        if ($leaveType->requires_document && !$request->hasFile('attachment')) {
            return ApiResponse::error(__('attendance.leave_document_required', ['type' => $leaveType->name]), status: 422);
        }

        // Calculate total days (excluding weekends by default)
        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date']);
        $totalDays = 0;
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if ($date->isWeekday()) {
                $totalDays++;
            }
        }
        if ($totalDays === 0) {
            $totalDays = 1; // Minimum 1 day
        }

        // Check overlapping requests
        $status = $validated['status'] ?? 'submitted';
        if ($status === 'submitted') {
            $overlap = LeaveRequest::where('employee_id', $employeeId)
                ->whereIn('status', ['submitted', 'approved'])
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($inner) use ($validated) {
                          $inner->where('start_date', '<=', $validated['start_date'])
                                ->where('end_date', '>=', $validated['end_date']);
                      });
                })
                ->exists();

            if ($overlap) {
                return ApiResponse::error(__('attendance.leave_dates_overlap'), status: 422);
            }
        }

        return DB::transaction(function () use ($request, $user, $validated, $employeeId, $leaveType, $totalDays, $status) {
            // Upload attachment if present
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $ext  = $file->getClientOriginalExtension();
                $tenantId = $user->tenant_id;
                $uuid = Str::uuid()->toString();
                $attachmentPath = "leaves/{$tenantId}/{$employeeId}/{$uuid}.{$ext}";
                Storage::disk('local')->put($attachmentPath, file_get_contents($file->getRealPath()));
            }

            $leaveRequest = LeaveRequest::create([
                'tenant_id'       => $user->tenant_id,
                'employee_id'     => $employeeId,
                'leave_type_id'   => $leaveType->id,
                'start_date'      => $validated['start_date'],
                'end_date'        => $validated['end_date'],
                'total_days'      => $totalDays,
                'reason'          => $validated['reason'] ?? null,
                'attachment_path' => $attachmentPath,
                'status'          => $status,
                'submitted_at'    => $status === 'submitted' ? now() : null,
            ]);

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => $status === 'submitted' ? 'leave.submitted' : 'leave.drafted',
                'entity_type'   => 'LeaveRequest',
                'entity_id'     => $leaveRequest->id,
                'new_values'    => $leaveRequest->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify approvers if submitted
            if ($status === 'submitted') {
                $employee = Employee::find($employeeId);
                $this->notifyApprovers($user, $employee, $leaveRequest, $leaveType);
            }

            return ApiResponse::created(
                $leaveRequest->load(['employee:id,name,employee_number', 'leaveType:id,name,code']),
                __('attendance.leave_submitted')
            );
        });
    }

    /**
     * Show leave request detail.
     */
    public function show(string $id): JsonResponse
    {
        $user = auth()->user();
        $leaveRequest = LeaveRequest::with([
            'employee:id,name,employee_number,department_id,team_id',
            'leaveType',
            'reviewer:id,name',
        ])->findOrFail($id);

        if (!$this->canViewLeave($user, $leaveRequest)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        return ApiResponse::success($leaveRequest);
    }

    /**
     * Approve leave request and update attendance records.
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canApproveLeaves($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $leaveRequest = LeaveRequest::with(['employee.user', 'leaveType'])->findOrFail($id);

        if ($leaveRequest->status !== 'submitted') {
            return ApiResponse::error(__('attendance.leave_already_processed'), status: 422);
        }

        return DB::transaction(function () use ($request, $user, $leaveRequest, $validated) {
            $oldValues = $leaveRequest->toArray();

            $leaveRequest->update([
                'status'       => 'approved',
                'reviewed_by'  => $user->id,
                'reviewed_at'  => now(),
                'review_notes' => $validated['review_notes'] ?? null,
            ]);

            // Create or update attendance records for the leave period
            $start = Carbon::parse($leaveRequest->start_date);
            $end   = Carbon::parse($leaveRequest->end_date);
            $leaveStatus = $leaveRequest->leaveType->is_paid ? 'leave' : 'permission';

            foreach (CarbonPeriod::create($start, $end) as $date) {
                if ($date->isWeekday()) {
                    $dateStr = $date->toDateString();
                    AttendanceRecord::updateOrCreate(
                        [
                            'tenant_id'       => $leaveRequest->tenant_id,
                            'employee_id'     => $leaveRequest->employee_id,
                            'attendance_date' => $dateStr,
                        ],
                        [
                            'status' => $leaveStatus,
                            'source' => 'system',
                            'notes'  => "Approved Leave: {$leaveRequest->leaveType->name}",
                        ]
                    );
                }
            }

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => 'leave.approved',
                'entity_type'   => 'LeaveRequest',
                'entity_id'     => $leaveRequest->id,
                'old_values'    => $oldValues,
                'new_values'    => $leaveRequest->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify employee
            $employee = $leaveRequest->employee;
            if ($employee && $employee->user) {
                $this->notificationService->send(
                    $employee->user,
                    'leave_approved',
                    __('attendance.leave_approved_title'),
                    __('attendance.leave_approved_body', [
                        'type'       => $leaveRequest->leaveType->name,
                        'start_date' => $leaveRequest->start_date->format('Y-m-d'),
                        'end_date'   => $leaveRequest->end_date->format('Y-m-d'),
                    ]),
                    ['leave_id' => $leaveRequest->id]
                );
            }

            return ApiResponse::success(
                $leaveRequest->fresh(['employee:id,name,employee_number', 'leaveType', 'reviewer:id,name']),
                __('attendance.leave_approved')
            );
        });
    }

    /**
     * Reject leave request.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $user = auth()->user();
        if (!$this->canApproveLeaves($user)) {
            return ApiResponse::error(__('auth.forbidden'), status: 403);
        }

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:1000'],
        ]);

        $leaveRequest = LeaveRequest::with(['employee.user', 'leaveType'])->findOrFail($id);

        if ($leaveRequest->status !== 'submitted') {
            return ApiResponse::error(__('attendance.leave_already_processed'), status: 422);
        }

        return DB::transaction(function () use ($request, $user, $leaveRequest, $validated) {
            $oldValues = $leaveRequest->toArray();

            $leaveRequest->update([
                'status'       => 'rejected',
                'reviewed_by'  => $user->id,
                'reviewed_at'  => now(),
                'review_notes' => $validated['review_notes'],
            ]);

            // Audit log
            AuditLog::create([
                'tenant_id'     => $user->tenant_id,
                'actor_user_id' => $user->id,
                'action'        => 'leave.rejected',
                'entity_type'   => 'LeaveRequest',
                'entity_id'     => $leaveRequest->id,
                'old_values'    => $oldValues,
                'new_values'    => $leaveRequest->toArray(),
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
            ]);

            // Notify employee
            $employee = $leaveRequest->employee;
            if ($employee && $employee->user) {
                $this->notificationService->send(
                    $employee->user,
                    'leave_rejected',
                    __('attendance.leave_rejected_title'),
                    __('attendance.leave_rejected_body', [
                        'type'   => $leaveRequest->leaveType->name,
                        'reason' => $validated['review_notes'],
                    ]),
                    ['leave_id' => $leaveRequest->id]
                );
            }

            return ApiResponse::success(
                $leaveRequest->fresh(['employee:id,name,employee_number', 'leaveType', 'reviewer:id,name']),
                __('attendance.leave_rejected')
            );
        });
    }

    // ---------------------
    // Helper Methods
    // ---------------------

    private function canApproveLeaves(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'owner', 'hr_admin', 'manager'])
            || $user->hasPermissionTo('leaves.approve');
    }

    private function isEmployeeOnly(User $user): bool
    {
        return $user->hasAnyRole(['employee', 'field_worker'])
            && !$user->hasAnyRole(['manager', 'supervisor', 'hr_admin', 'owner', 'super_admin'])
            && !$user->hasPermissionTo('leaves.approve');
    }

    private function isManagerOrSupervisor(User $user): bool
    {
        return $user->hasAnyRole(['manager', 'supervisor'])
            && !$user->hasAnyRole(['hr_admin', 'owner', 'super_admin']);
    }

    private function canViewLeave(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($this->canApproveLeaves($user) || $user->hasRole('super_admin')) {
            return true;
        }

        $userEmployee = $user->employee;
        if (!$userEmployee) {
            return false;
        }

        // Own request
        if ($leaveRequest->employee_id === $userEmployee->id) {
            return true;
        }

        // Manager over team or department
        if ($this->isManagerOrSupervisor($user)) {
            $reqEmployee = $leaveRequest->employee;
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

    private function notifyApprovers(User $actor, ?Employee $employee, LeaveRequest $leaveRequest, LeaveType $leaveType): void
    {
        $approvers = User::where('tenant_id', $actor->tenant_id)
            ->where(function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->whereIn('name', ['hr_admin', 'owner', 'super_admin']);
                })->orWhereHas('permissions', function ($pq) {
                    $pq->where('name', 'leaves.approve');
                });
            })
            ->where('id', '!=', $actor->id)
            ->get();

        $empName = $employee ? $employee->name : $actor->name;
        $startStr = Carbon::parse($leaveRequest->start_date)->format('Y-m-d');
        $endStr   = Carbon::parse($leaveRequest->end_date)->format('Y-m-d');

        $this->notificationService->sendToMany(
            $approvers,
            'leave_request',
            __('attendance.leave_notification_title'),
            __('attendance.leave_notification_body', [
                'name'  => $empName,
                'type'  => $leaveType->name,
                'start' => $startStr,
                'end'   => $endStr,
            ]),
            ['leave_id' => $leaveRequest->id]
        );
    }
}
