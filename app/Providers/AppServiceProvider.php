<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Domain\SaaS\EntitlementService::class);
        $this->app->bind(
            \App\Integrations\Fingerprint\Contracts\FingerprintProvider::class,
            \App\Integrations\Fingerprint\XSolutions\XSolutionsProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(\App\Models\Employee::class, \App\Policies\EmployeePolicy::class);
        Gate::policy(\App\Models\Task::class, \App\Policies\TaskPolicy::class);
        Gate::policy(\App\Models\Target::class, \App\Policies\TargetPolicy::class);
        Gate::policy(\App\Models\AttendanceRecord::class, \App\Policies\AttendancePolicy::class);

        Gate::define('platform.tenants.manage', function (User $user) {
            return $user->hasRole('super_admin') || $user->hasPermissionTo('platform.tenants.manage');
        });

        Gate::define('organization.manage', fn(User $user) => $user->hasPermissionTo('organization.manage'));
        Gate::define('branches.view', fn(User $user) => $user->hasPermissionTo('branches.view'));
        Gate::define('branches.manage', fn(User $user) => $user->hasPermissionTo('branches.manage'));
        Gate::define('organization.view', fn(User $user) => $user->hasPermissionTo('organization.view'));
        Gate::define('departments.view', fn(User $user) => $user->hasPermissionTo('departments.view'));
        Gate::define('departments.manage', fn(User $user) => $user->hasPermissionTo('departments.manage'));
        Gate::define('teams.view', fn(User $user) => $user->hasPermissionTo('teams.view'));
        Gate::define('teams.manage', fn(User $user) => $user->hasPermissionTo('teams.manage'));
        Gate::define('positions.view', fn(User $user) => $user->hasPermissionTo('positions.view'));
        Gate::define('positions.manage', fn(User $user) => $user->hasPermissionTo('positions.manage'));
        Gate::define('work-locations.view', fn(User $user) => $user->hasAnyPermission(['view work locations', 'manage work locations']));
        Gate::define('work-locations.manage', fn(User $user) => $user->hasPermissionTo('manage work locations'));

        // Employee gates
        Gate::define('employees.view', fn(User $user) => $user->hasAnyPermission(['view employees', 'manage employees']));
        Gate::define('employees.manage', fn(User $user) => $user->hasPermissionTo('manage employees'));

        // Attendance gates
        Gate::define('shifts.view', fn(User $user) => $user->hasAnyPermission(['view shifts', 'manage shifts']));
        Gate::define('shifts.manage', fn(User $user) => $user->hasPermissionTo('manage shifts'));
        Gate::define('schedules.view', fn(User $user) => $user->hasAnyPermission(['view schedules', 'manage schedules']));
        Gate::define('schedules.manage', fn(User $user) => $user->hasPermissionTo('manage schedules'));
        Gate::define('attendance.records.view', fn(User $user) => $user->hasAnyPermission(['view attendance', 'manage attendance']));
        Gate::define('attendance.records.manage', fn(User $user) => $user->hasPermissionTo('manage attendance'));
    }
}
