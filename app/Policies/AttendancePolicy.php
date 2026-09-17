<?php
namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['attendance.view_all', 'attendance.view_team', 'attendance.view_own']);
    }

    public function view(User $user, AttendanceRecord $record): bool
    {
        if ($user->tenant_id !== $record->tenant_id) return false;
        if ($user->hasPermissionTo('attendance.view_all')) return true;
        if ($user->hasPermissionTo('attendance.view_team')) return true; // scoped in query
        // view_own: only their own attendance
        $employee = $user->employee;
        return $employee && $record->employee_id === $employee->id;
    }

    public function checkIn(User $user): bool
    {
        return $user->hasPermissionTo('attendance.check_in');
    }
}
