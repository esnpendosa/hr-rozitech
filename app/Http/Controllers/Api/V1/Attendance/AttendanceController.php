<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Application\Services\ApiResponse;
use App\Domain\Attendance\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\WorkLocation;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    /**
     * Record check-in for the authenticated employee.
     */
    public function checkIn(Request $request): JsonResponse
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return ApiResponse::error(__('attendance.no_employee_record'), status: 422);
        }

        $validated = $request->validate([
            'latitude'         => ['required', 'numeric', 'between:-90,90'],
            'longitude'        => ['required', 'numeric', 'between:-180,180'],
            'work_location_id' => ['nullable', 'uuid', 'exists:work_locations,id'],
            'photo'            => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if already checked in today
        $today = Carbon::today();
        $existing = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in_at !== null) {
            return ApiResponse::error(__('attendance.already_checked_in'));
        }

        return DB::transaction(function () use ($request, $validated, $employee, $today, $existing) {
            $lat = (float) $validated['latitude'];
            $lng = (float) $validated['longitude'];

            // Geofence check
            $isOutsideGeofence = false;
            $workLocationId    = $validated['work_location_id'] ?? null;

            if ($workLocationId) {
                $workLocation = WorkLocation::find($workLocationId);
                if ($workLocation) {
                    $isOutsideGeofence = $this->attendanceService->isOutsideGeofence($workLocation, $lat, $lng);
                }
            }

            // Determine scheduled shift to calculate late minutes
            $lateMinutes = 0;
            $status      = 'present';
            $workScheduleId = null;

            $schedule = $this->findTodaySchedule($employee->id, $today);
            if ($schedule && $schedule->shift) {
                $workScheduleId = $schedule->id;
                $shift          = $schedule->shift;
                $toleranceMin   = $shift->tolerance_late_minutes ?? 0;
                $lateMinutes    = $this->attendanceService->calculateLateMinutes($shift, Carbon::now(), $toleranceMin);
                $status         = $this->attendanceService->determineStatus($lateMinutes);
            }

            // Store photo privately if uploaded
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file      = $request->file('photo');
                $ext       = $file->getClientOriginalExtension();
                $tenantId  = $employee->tenant_id;
                $dateStr   = $today->format('Y-m-d');
                $photoPath = "attendance/{$tenantId}/{$employee->id}/{$dateStr}/checkin.{$ext}";
                Storage::disk('local')->put($photoPath, file_get_contents($file->getRealPath()));
            }

            $now = Carbon::now();

            // Create or update today's record (UNIQUE tenant+employee+date)
            $record = AttendanceRecord::updateOrCreate(
                [
                    'employee_id'     => $employee->id,
                    'attendance_date' => $today->toDateString(),
                ],
                [
                    'work_schedule_id'  => $workScheduleId,
                    'check_in_at'       => $now,
                    'check_in_lat'      => $lat,
                    'check_in_lng'      => $lng,
                    'check_in_photo'    => $photoPath,
                    'status'            => $status,
                    'late_minutes'      => $lateMinutes,
                    'source'            => 'mobile',
                    'work_location_id'  => $workLocationId,
                    'is_outside_geofence' => $isOutsideGeofence,
                    'notes'             => $validated['notes'] ?? null,
                ]
            );

            // Create attendance event
            AttendanceEvent::create([
                'employee_id'         => $employee->id,
                'event_type'          => 'check_in',
                'event_time'          => $now,
                'latitude'            => $lat,
                'longitude'           => $lng,
                'source'              => 'mobile',
                'processed'           => true,
                'attendance_record_id' => $record->id,
            ]);

            $record->load(['employee', 'workLocation']);

            return ApiResponse::created($record, __('attendance.check_in_success'));
        });
    }

    /**
     * Record check-out for the authenticated employee.
     */
    public function checkOut(Request $request): JsonResponse
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return ApiResponse::error(__('attendance.no_employee_record'), status: 422);
        }

        $validated = $request->validate([
            'latitude'         => ['required', 'numeric', 'between:-90,90'],
            'longitude'        => ['required', 'numeric', 'between:-180,180'],
            'work_location_id' => ['nullable', 'uuid', 'exists:work_locations,id'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $today  = Carbon::today();
        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$record || $record->check_in_at === null) {
            return ApiResponse::error(__('attendance.not_checked_in'));
        }

        return DB::transaction(function () use ($validated, $employee, $record, $today) {
            $lat = (float) $validated['latitude'];
            $lng = (float) $validated['longitude'];
            $now = Carbon::now();

            // Geofence check
            $isOutsideGeofence = false;
            $workLocationId    = $validated['work_location_id'] ?? $record->work_location_id;

            if ($workLocationId) {
                $workLocation = WorkLocation::find($workLocationId);
                if ($workLocation) {
                    $isOutsideGeofence = $this->attendanceService->isOutsideGeofence($workLocation, $lat, $lng);
                }
            }

            // Calculate early_leave and overtime if shift is available
            $earlyLeaveMinutes = 0;
            $overtimeMinutes   = 0;

            $schedule = $this->findTodaySchedule($employee->id, $today);
            if ($schedule && $schedule->shift) {
                $shift          = $schedule->shift;
                $toleranceEarly = $shift->tolerance_early_leave_minutes ?? 0;
                $overtimeThresh = $shift->overtime_threshold_minutes ?? 0;

                $earlyLeaveMinutes = $this->attendanceService->calculateEarlyLeaveMinutes($shift, $now, $toleranceEarly);
                $overtimeMinutes   = $this->attendanceService->calculateOvertimeMinutes($shift, $now, $overtimeThresh);
            }

            $updateData = [
                'check_out_at'        => $now,
                'check_out_lat'       => $lat,
                'check_out_lng'       => $lng,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'overtime_minutes'    => $overtimeMinutes,
            ];

            if ($validated['notes'] ?? null) {
                $updateData['notes'] = $validated['notes'];
            }

            if ($isOutsideGeofence) {
                $updateData['is_outside_geofence'] = true;
            }

            $record->update($updateData);

            // Create attendance event
            AttendanceEvent::create([
                'employee_id'          => $employee->id,
                'event_type'           => 'check_out',
                'event_time'           => $now,
                'latitude'             => $lat,
                'longitude'            => $lng,
                'source'               => 'mobile',
                'processed'            => true,
                'attendance_record_id' => $record->id,
            ]);

            $record->load(['employee', 'workLocation']);

            return ApiResponse::success($record, __('attendance.check_out_success'));
        });
    }

    /**
     * Get today's attendance record for the authenticated employee.
     */
    public function today(Request $request): JsonResponse
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return ApiResponse::error(__('attendance.no_employee_record'), status: 422);
        }

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->with(['employee', 'workLocation', 'workSchedule.shift'])
            ->first();

        return ApiResponse::success($record);
    }

    // ---------------------
    // Private helpers
    // ---------------------

    /**
     * Find the most applicable work schedule for an employee on a given date.
     * Checks for exact date match first, then recurring schedules.
     */
    private function findTodaySchedule(\App\Models\Employee $employee, Carbon $date): ?WorkSchedule
    {
        // 1. Exact date match
        $exact = WorkSchedule::where('employee_id', $employee->id)
            ->whereDate('schedule_date', $date)
            ->with('shift')
            ->first();

        if ($exact) {
            return $exact;
        }

        // 2. Recurring schedule valid on this date (weekly)
        $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 6=Saturday

        $recurring = WorkSchedule::where('employee_id', $employee->id)
            ->whereNull('schedule_date')
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $date->toDateString());
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', $date->toDateString());
            })
            ->where('recurrence_type', 'weekly')
            ->with('shift')
            ->get();

        foreach ($recurring as $schedule) {
            $days = json_decode($schedule->recurrence_days ?? '[]', true);
            if (in_array($dayOfWeek, (array) $days, true)) {
                return $schedule;
            }
        }

        // 3. Daily recurring
        $daily = WorkSchedule::where('employee_id', $employee->id)
            ->whereNull('schedule_date')
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $date->toDateString());
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', $date->toDateString());
            })
            ->where('recurrence_type', 'daily')
            ->with('shift')
            ->first();

        return $daily;
    }
}
