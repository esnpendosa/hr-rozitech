<?php
namespace App\Policies;

use App\Models\Target;
use App\Models\User;

class TargetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['targets.view_all', 'targets.view_team', 'targets.view_own']);
    }

    public function view(User $user, Target $target): bool
    {
        if ($user->tenant_id !== $target->tenant_id) return false;
        if ($user->hasPermissionTo('targets.view_all')) return true;
        return $user->hasAnyPermission(['targets.view_team', 'targets.view_own']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('targets.create');
    }

    public function update(User $user, Target $target): bool
    {
        return $user->tenant_id === $target->tenant_id
            && $user->hasPermissionTo('targets.edit');
    }

    public function updateProgress(User $user, Target $target): bool
    {
        return $user->tenant_id === $target->tenant_id
            && $user->hasPermissionTo('targets.update_progress');
    }
}
