<?php
namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['reports.view_all', 'reports.view_team']);
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('reports.export');
    }
}
