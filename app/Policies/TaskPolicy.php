<?php
namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['tasks.view_all', 'tasks.view_team', 'tasks.view_own']);
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->tenant_id !== $task->tenant_id) return false;
        if ($user->hasPermissionTo('tasks.view_all')) return true;
        if ($user->hasPermissionTo('tasks.view_team')) {
            // Manager/Supervisor can see tasks they assigned or in their team
            return $task->assigned_by === $user->id
                || $task->assignees()->whereHas('employee', fn($q) => $q->where('user_id', $user->id))->exists();
        }
        // view_own: only tasks assigned to them
        return $task->assignees()->whereHas('employee', fn($q) => $q->where('user_id', $user->id))->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        return $user->tenant_id === $task->tenant_id
            && $user->hasPermissionTo('tasks.edit');
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->tenant_id === $task->tenant_id
            && $user->hasPermissionTo('tasks.delete');
    }

    public function updateStatus(User $user, Task $task): bool
    {
        return $user->tenant_id === $task->tenant_id
            && $user->hasPermissionTo('tasks.update_status');
    }
}
