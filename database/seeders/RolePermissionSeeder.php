<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Tenant/Platform
            'platform.tenants.manage',
            'platform.plans.manage',

            // Organization
            'organization.view', 'organization.manage',
            'branches.view', 'branches.manage',
            'departments.view', 'departments.manage',
            'teams.view', 'teams.manage',
            'positions.view', 'positions.manage',
            'work_locations.view', 'work_locations.manage',

            // Employees
            'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            'employees.view_own',
            'employees.documents.view', 'employees.documents.upload',

            // Attendance
            'attendance.view_all', 'attendance.view_team', 'attendance.view_own',
            'attendance.check_in', 'attendance.corrections.manage',
            'attendance.corrections.request',
            'work_shifts.manage', 'work_schedules.manage',
            'leaves.view_all', 'leaves.view_team', 'leaves.request', 'leaves.approve',
            'overtime.view_all', 'overtime.request', 'overtime.approve',

            // Fingerprint
            'fingerprint.devices.manage', 'fingerprint.sync',

            // Tasks
            'tasks.view_all', 'tasks.view_team', 'tasks.view_own',
            'tasks.create', 'tasks.edit', 'tasks.delete', 'tasks.assign',
            'tasks.update_status',

            // Projects
            'projects.view', 'projects.manage',

            // Targets
            'targets.view_all', 'targets.view_team', 'targets.view_own',
            'targets.create', 'targets.edit', 'targets.update_progress',

            // KPI
            'kpi.templates.manage', 'kpi.assign', 'kpi.input_actuals', 'kpi.view',

            // Performance
            'performance.view_all', 'performance.view_team', 'performance.configure',

            // Customers & Field Work
            'customers.view', 'customers.manage',
            'field_jobs.view_all', 'field_jobs.view_own', 'field_jobs.assign', 'field_jobs.update',

            // Reports
            'reports.view_all', 'reports.view_team', 'reports.export',

            // Notifications
            'notifications.manage',

            // Settings
            'settings.company.manage', 'settings.users.manage',
            'roles.manage', 'permissions.manage',
            'subscription.manage',

            // Audit
            'audit_logs.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Define roles and their permissions
        $rolePermissions = [
            'super_admin' => $permissions, // all permissions

            'owner' => [
                'organization.view', 'organization.manage',
                'branches.view', 'branches.manage',
                'departments.view', 'departments.manage',
                'teams.view', 'teams.manage',
                'positions.view', 'positions.manage',
                'work_locations.view', 'work_locations.manage',
                'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
                'employees.documents.view', 'employees.documents.upload',
                'attendance.view_all', 'attendance.corrections.manage',
                'work_shifts.manage', 'work_schedules.manage',
                'leaves.view_all', 'leaves.approve',
                'overtime.view_all', 'overtime.approve',
                'fingerprint.devices.manage', 'fingerprint.sync',
                'tasks.view_all', 'tasks.create', 'tasks.edit', 'tasks.delete', 'tasks.assign',
                'projects.view', 'projects.manage',
                'targets.view_all', 'targets.create', 'targets.edit',
                'kpi.templates.manage', 'kpi.assign', 'kpi.view',
                'performance.view_all', 'performance.configure',
                'customers.view', 'customers.manage',
                'field_jobs.view_all', 'field_jobs.assign',
                'reports.view_all', 'reports.export',
                'settings.company.manage', 'settings.users.manage',
                'roles.manage', 'permissions.manage',
                'subscription.manage',
                'audit_logs.view',
            ],

            'hr_admin' => [
                'organization.view', 'branches.view', 'departments.view', 'teams.view',
                'positions.view', 'positions.manage', 'work_locations.view', 'work_locations.manage',
                'employees.view', 'employees.create', 'employees.edit',
                'employees.documents.view', 'employees.documents.upload',
                'attendance.view_all', 'attendance.corrections.manage',
                'work_shifts.manage', 'work_schedules.manage',
                'leaves.view_all', 'leaves.approve',
                'overtime.view_all', 'overtime.approve',
                'fingerprint.devices.manage', 'fingerprint.sync',
                'tasks.view_all',
                'reports.view_all', 'reports.export',
            ],

            'manager' => [
                'organization.view', 'branches.view', 'departments.view', 'teams.view', 'teams.manage',
                'employees.view', 'employees.view_own',
                'attendance.view_team', 'attendance.corrections.manage',
                'work_schedules.manage',
                'leaves.view_team', 'leaves.approve',
                'overtime.view_all', 'overtime.approve',
                'tasks.view_team', 'tasks.create', 'tasks.edit', 'tasks.assign', 'tasks.update_status',
                'projects.view', 'projects.manage',
                'targets.view_team', 'targets.create', 'targets.edit', 'targets.update_progress',
                'kpi.assign', 'kpi.input_actuals', 'kpi.view',
                'performance.view_team',
                'customers.view', 'customers.manage',
                'field_jobs.view_all', 'field_jobs.assign',
                'reports.view_team', 'reports.export',
            ],

            'supervisor' => [
                'organization.view', 'teams.view',
                'employees.view',
                'attendance.view_team',
                'tasks.view_team', 'tasks.create', 'tasks.assign', 'tasks.update_status',
                'targets.view_team', 'targets.update_progress',
                'kpi.view',
                'customers.view',
                'field_jobs.view_all', 'field_jobs.assign', 'field_jobs.update',
                'reports.view_team',
            ],

            'employee' => [
                'employees.view_own',
                'attendance.view_own', 'attendance.check_in', 'attendance.corrections.request',
                'leaves.request',
                'overtime.request',
                'tasks.view_own', 'tasks.update_status',
                'targets.view_own', 'targets.update_progress',
                'kpi.view',
            ],

            'field_worker' => [
                'employees.view_own',
                'attendance.view_own', 'attendance.check_in', 'attendance.corrections.request',
                'leaves.request',
                'overtime.request',
                'tasks.view_own', 'tasks.update_status',
                'targets.view_own', 'targets.update_progress',
                'kpi.view',
                'customers.view',
                'field_jobs.view_own', 'field_jobs.update',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
