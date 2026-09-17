<?php

namespace App\Domain\Attendance\Services;

use App\Models\WorkLocation;
use App\Models\WorkShift;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Calculate distance between two GPS coordinates using the Haversine formula.
     * Returns distance in meters.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check whether the given coordinates are outside the geofence of a work location.
     *
     * Returns true if OUTSIDE the geofence.
     * Returns false when no geofence is configured (location has no lat/lng/radius).
     */
    public function isOutsideGeofence(WorkLocation $location, float $lat, float $lng): bool
    {
        if (!$location->latitude || !$location->longitude || !$location->radius_meters) {
            return false; // no geofence configured — always considered valid
        }

        $distance = $this->haversineDistance(
            $location->latitude,
            $location->longitude,
            $lat,
            $lng
        );

        return $distance > $location->radius_meters;
    }

    /**
     * Calculate how many minutes an employee checked in late relative to the shift start time.
     * Applies a tolerance window before marking as late.
     * Returns 0 if not late.
     */
    public function calculateLateMinutes(WorkShift $shift, Carbon $checkInAt, int $toleranceMinutes = 0): int
    {
        $scheduleStart = Carbon::parse(
            $checkInAt->format('Y-m-d') . ' ' . $shift->start_time
        );

        // Positive = late, negative = early
        $lateBy = $checkInAt->diffInMinutes($scheduleStart, false);

        return max(0, $lateBy - $toleranceMinutes);
    }

    /**
     * Calculate how many minutes an employee left early relative to the shift end time.
     * Applies a tolerance window before marking as early leave.
     * Returns 0 if not early.
     */
    public function calculateEarlyLeaveMinutes(WorkShift $shift, Carbon $checkOutAt, int $toleranceMinutes = 0): int
    {
        $scheduleEnd = Carbon::parse(
            $checkOutAt->format('Y-m-d') . ' ' . $shift->end_time
        );

        // For overnight shifts, end time is on the next day
        if ($shift->is_overnight && $scheduleEnd->lte($checkOutAt->copy()->startOfDay())) {
            $scheduleEnd->addDay();
        }

        // Positive = early leave, negative = still within/after shift
        $earlyBy = $scheduleEnd->diffInMinutes($checkOutAt, false);

        return max(0, $earlyBy - $toleranceMinutes);
    }

    /**
     * Calculate overtime minutes: how long after the shift end + threshold an employee worked.
     * Returns 0 if no overtime.
     */
    public function calculateOvertimeMinutes(WorkShift $shift, Carbon $checkOutAt, int $thresholdMinutes = 0): int
    {
        $scheduleEnd = Carbon::parse(
            $checkOutAt->format('Y-m-d') . ' ' . $shift->end_time
        );

        // For overnight shifts, end time is on the next day
        if ($shift->is_overnight && $scheduleEnd->lte($checkOutAt->copy()->startOfDay())) {
            $scheduleEnd->addDay();
        }

        // How many minutes after end the employee checked out
        $overtimeBy = $scheduleEnd->diffInMinutes($checkOutAt, false);

        // Only count if they exceeded the threshold
        if ($overtimeBy > $thresholdMinutes) {
            return $overtimeBy;
        }

        return 0;
    }

    /**
     * Determine the attendance status string based on late minutes.
     */
    public function determineStatus(int $lateMinutes): string
    {
        return $lateMinutes > 0 ? 'late' : 'present';
    }

    /**
     * Find the most applicable work schedule for an employee on a given date.
     * Checks employee-specific schedules first, then team-level schedules.
     */
    public function findScheduleForDate(\App\Models\Employee $employee, Carbon $date): ?\App\Models\WorkSchedule
    {
        // 1. Exact date match (employee)
        $exact = \App\Models\WorkSchedule::where('employee_id', $employee->id)
            ->whereDate('schedule_date', $date)
            ->with('shift')
            ->first();

        if ($exact) {
            return $exact;
        }

        // 2. Exact date match (team)
        if ($employee->team_id) {
            $exactTeam = \App\Models\WorkSchedule::where('team_id', $employee->team_id)
                ->whereDate('schedule_date', $date)
                ->with('shift')
                ->first();

            if ($exactTeam) {
                return $exactTeam;
            }
        }

        $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 6=Saturday

        // 3. Recurring schedule valid on this date (weekly)
        $recurring = \App\Models\WorkSchedule::where(function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
                if ($employee->team_id) {
                    $q->orWhere('team_id', $employee->team_id);
                }
            })
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
            ->orderByRaw('employee_id IS NULL ASC') // Employee schedule takes precedence over team
            ->get();

        foreach ($recurring as $schedule) {
            $days = is_array($schedule->recurrence_days)
                ? $schedule->recurrence_days
                : json_decode($schedule->recurrence_days ?? '[]', true);
            if (in_array($dayOfWeek, (array) $days, true)) {
                return $schedule;
            }
        }

        // 4. Daily recurring
        return \App\Models\WorkSchedule::where(function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
                if ($employee->team_id) {
                    $q->orWhere('team_id', $employee->team_id);
                }
            })
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
            ->orderByRaw('employee_id IS NULL ASC')
            ->first();
    }
}
