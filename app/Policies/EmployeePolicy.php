<?php
namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['employees.view', 'employees.view_own']);
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasPermissionTo('employees.view')) {
            return $user->tenant_id === $employee->tenant_id;
        }
        // view_own: only their own employee record
        return $user->tenant_id === $employee->tenant_id
            && $employee->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('employees.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('employees.edit')
            && $user->tenant_id === $employee->tenant_id;
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('employees.delete')
            && $user->tenant_id === $employee->tenant_id;
    }
}
