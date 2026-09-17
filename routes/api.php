<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Organization\BranchController;
use App\Http\Controllers\Api\V1\Organization\CompanyController;
use App\Http\Controllers\Api\V1\Organization\DepartmentController;
use App\Http\Controllers\Api\V1\Organization\DivisionController;
use App\Http\Controllers\Api\V1\Organization\PositionController;
use App\Http\Controllers\Api\V1\Organization\TeamController;
use App\Http\Controllers\Api\V1\Employee\EmployeeContractController;
use App\Http\Controllers\Api\V1\Employee\EmployeeController;
use App\Http\Controllers\Api\V1\Employee\EmployeeDocumentController;
use App\Http\Controllers\Api\V1\Employee\EmployeeEmergencyContactController;
use App\Http\Controllers\Api\V1\Employee\EmployeeHistoryController;
use App\Http\Controllers\Api\V1\Organization\WorkLocationController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceCorrectionController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceRecordController;
use App\Http\Controllers\Api\V1\Attendance\LeaveRequestController;
use App\Http\Controllers\Api\V1\Attendance\LeaveTypeController;
use App\Http\Controllers\Api\V1\Attendance\OvertimeController;
use App\Http\Controllers\Api\V1\Attendance\WorkShiftController;
use App\Http\Controllers\Api\V1\Attendance\WorkScheduleController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - RMIH Platform
|--------------------------------------------------------------------------
|
| API routes are versioned under /api/v1/
| All routes require authentication via Sanctum unless marked as public.
| Tenant context is resolved automatically from authenticated user.
|
*/

Route::prefix('v1')->name('api.')->group(function () {

    // ---------------------
    // Auth routes
    // ---------------------
    Route::prefix('auth')->name('api.auth.')->group(function () {

        // Public (no authentication)
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

        // Protected (requires valid Sanctum token + resolved tenant)
        Route::middleware(['auth:sanctum', 'resolve.tenant'])->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
            Route::get('me', [AuthController::class, 'me'])->name('me');
            Route::post('fcm-token', [AuthController::class, 'registerFcmToken'])->name('fcm-token');
            Route::get('sessions', [AuthController::class, 'sessions'])->name('sessions');
            Route::delete('sessions/{sessionId}', [AuthController::class, 'revokeSession'])->name('sessions.revoke');
        });
    });

    // ---------------------
    // Platform routes (Super Admin)
    // ---------------------
    Route::prefix('platform')->middleware(['auth:sanctum'])->group(function () {
        Route::get('tenants', [TenantController::class, 'index']);
        Route::post('tenants', [TenantController::class, 'store']);
        Route::get('tenants/{id}', [TenantController::class, 'show']);
        Route::put('tenants/{id}', [TenantController::class, 'update']);
        Route::post('tenants/{id}/suspend', [TenantController::class, 'suspend']);
        Route::post('tenants/{id}/activate', [TenantController::class, 'activate']);
    });

    // Public tenant registration
    Route::post('register', [TenantController::class, 'store'])->name('api.register');

    // ---------------------
    // Protected routes (other modules – added by subsequent tasks)
    // ---------------------
    Route::middleware(['auth:sanctum', 'resolve.tenant'])->group(function () {
        // Employee routes will be added in task 10
        // Attendance routes will be added in task 11
        // Task routes will be added later
        // Notification routes will be added later

        // Subscription routes
        Route::get('subscription', [SubscriptionController::class, 'show']);
        Route::get('subscription/plans', [SubscriptionController::class, 'plans']);

        // Organization
        Route::prefix('organization')->group(function () {
            Route::get('company', [CompanyController::class, 'show']);
            Route::post('company', [CompanyController::class, 'store']);
        });

        Route::apiResource('branches', BranchController::class);

        // Organization — Departments, Divisions, Teams, Positions, Work Locations
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('divisions', DivisionController::class);
        Route::apiResource('teams', TeamController::class);
        Route::apiResource('positions', PositionController::class);
        
        Route::prefix('organization')->group(function () {
            Route::apiResource('work-locations', WorkLocationController::class);
        });

        // Employees
        Route::apiResource('employees', EmployeeController::class);

        // Attendance — Shifts & Schedules
        Route::apiResource('attendance/shifts', WorkShiftController::class)->names('shifts');
        Route::apiResource('attendance/schedules', WorkScheduleController::class)->names('schedules');

        // Attendance — Check-in / Check-out
        Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('attendance/today', [AttendanceController::class, 'today']);

        // Attendance — Records & Calendar (summary/calendar before {id} to avoid route conflict)
        Route::get('attendance/records', [AttendanceRecordController::class, 'index']);
        Route::get('attendance/records/summary', [AttendanceRecordController::class, 'summary']);
        Route::get('attendance/records/calendar', [AttendanceRecordController::class, 'calendar']);
        Route::get('attendance/records/{id}', [AttendanceRecordController::class, 'show']);

        // Attendance — Corrections
        Route::get('attendance/corrections', [AttendanceCorrectionController::class, 'index']);
        Route::post('attendance/corrections', [AttendanceCorrectionController::class, 'store']);
        Route::get('attendance/corrections/{id}', [AttendanceCorrectionController::class, 'show']);
        Route::put('attendance/corrections/{id}/approve', [AttendanceCorrectionController::class, 'approve']);
        Route::put('attendance/corrections/{id}/reject', [AttendanceCorrectionController::class, 'reject']);

        // Leave & Permission Management
        Route::apiResource('leave-types', LeaveTypeController::class);
        Route::get('leaves', [LeaveRequestController::class, 'index']);
        Route::post('leaves', [LeaveRequestController::class, 'store']);
        Route::get('leaves/{id}', [LeaveRequestController::class, 'show']);
        Route::put('leaves/{id}/approve', [LeaveRequestController::class, 'approve']);
        Route::put('leaves/{id}/reject', [LeaveRequestController::class, 'reject']);

        // Overtime Management
        Route::get('overtime', [OvertimeController::class, 'index']);
        Route::post('overtime', [OvertimeController::class, 'store']);
        Route::get('overtime/{id}', [OvertimeController::class, 'show']);
        Route::put('overtime/{id}/approve', [OvertimeController::class, 'approve']);
        Route::put('overtime/{id}/reject', [OvertimeController::class, 'reject']);

        // Fingerprint Integration
        Route::prefix('fingerprint')->group(function () {
            Route::get('devices', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'index']);
            Route::post('devices', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'store']);
            Route::get('devices/{id}', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'show']);
            Route::put('devices/{id}', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'update']);
            Route::delete('devices/{id}', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'destroy']);
            Route::post('devices/{id}/test', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'testConnection']);
            Route::post('devices/{id}/sync', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'triggerSync']);
            Route::get('devices/{id}/logs', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'syncLogs']);

            Route::get('mappings', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'getMappings']);
            Route::post('mappings', [\App\Http\Controllers\Api\V1\Fingerprint\FingerprintDeviceController::class, 'mapEmployee']);
        });

        // Task Management
        Route::prefix('tasks')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Task\TaskController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\V1\Task\TaskController::class, 'store']);
            Route::get('{id}', [\App\Http\Controllers\Api\V1\Task\TaskController::class, 'show']);
            Route::put('{id}', [\App\Http\Controllers\Api\V1\Task\TaskController::class, 'update']);
            Route::delete('{id}', [\App\Http\Controllers\Api\V1\Task\TaskController::class, 'destroy']);
            Route::put('{id}/status', [\App\Http\Controllers\Api\V1\Task\TaskController::class, 'updateStatus']);

            // Task Checklists
            Route::post('{id}/checklists', [\App\Http\Controllers\Api\V1\Task\TaskChecklistAndCommentController::class, 'storeChecklist']);
            Route::put('{id}/checklists/{checklistId}', [\App\Http\Controllers\Api\V1\Task\TaskChecklistAndCommentController::class, 'toggleChecklist']);
            Route::delete('{id}/checklists/{checklistId}', [\App\Http\Controllers\Api\V1\Task\TaskChecklistAndCommentController::class, 'destroyChecklist']);

            // Task Comments
            Route::get('{id}/comments', [\App\Http\Controllers\Api\V1\Task\TaskChecklistAndCommentController::class, 'getComments']);
            Route::post('{id}/comments', [\App\Http\Controllers\Api\V1\Task\TaskChecklistAndCommentController::class, 'storeComment']);

            // Task Evidence
            Route::get('{id}/evidence', [\App\Http\Controllers\Api\V1\Task\TaskEvidenceController::class, 'index']);
            Route::post('{id}/evidence', [\App\Http\Controllers\Api\V1\Task\TaskEvidenceController::class, 'store']);
        });

        // Project Management
        Route::prefix('projects')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'store']);
            Route::get('{id}', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'show']);
            Route::put('{id}', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'update']);
            Route::delete('{id}', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'destroy']);
            Route::post('{id}/members', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'addMember']);
            Route::delete('{id}/members/{memberId}', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'removeMember']);
            Route::get('{id}/tasks', [\App\Http\Controllers\Api\V1\Project\ProjectController::class, 'getTasks']);
        });

        // Target Management
        Route::prefix('targets')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Target\TargetController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\V1\Target\TargetController::class, 'store']);
            Route::get('{id}', [\App\Http\Controllers\Api\V1\Target\TargetController::class, 'show']);
            Route::put('{id}', [\App\Http\Controllers\Api\V1\Target\TargetController::class, 'update']);
            Route::delete('{id}', [\App\Http\Controllers\Api\V1\Target\TargetController::class, 'destroy']);
            Route::post('{id}/progress', [\App\Http\Controllers\Api\V1\Target\TargetController::class, 'recordProgress']);
        });

        // KPI Engine
        Route::prefix('kpi')->group(function () {
            Route::get('templates', [\App\Http\Controllers\Api\V1\Kpi\KpiController::class, 'getTemplates']);
            Route::post('templates', [\App\Http\Controllers\Api\V1\Kpi\KpiController::class, 'storeTemplate']);
            Route::get('assignments', [\App\Http\Controllers\Api\V1\Kpi\KpiController::class, 'getAssignments']);
            Route::post('assignments', [\App\Http\Controllers\Api\V1\Kpi\KpiController::class, 'assignTemplate']);
            Route::post('assignments/{id}/actuals', [\App\Http\Controllers\Api\V1\Kpi\KpiController::class, 'submitActuals']);
        });

        // Performance Engine
        Route::prefix('performance')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Performance\PerformanceController::class, 'index']);
            Route::get('periods', [\App\Http\Controllers\Api\V1\Performance\PerformanceController::class, 'getPeriods']);
            Route::post('periods', [\App\Http\Controllers\Api\V1\Performance\PerformanceController::class, 'storePeriod']);
            Route::post('calculate', [\App\Http\Controllers\Api\V1\Performance\PerformanceController::class, 'calculate']);
        });

        // Customer & Field Work
        Route::prefix('customers')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'indexCustomers']);
            Route::post('/', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'storeCustomer']);
            Route::get('{id}', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'showCustomer']);
            Route::get('{id}/jobs', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'getCustomerJobs']);
        });

        Route::prefix('field-jobs')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'indexJobs']);
            Route::post('/', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'storeJob']);
            Route::put('{id}/checkin', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'checkInJob']);
            Route::put('{id}/complete', [\App\Http\Controllers\Api\V1\Customer\CustomerAndFieldJobController::class, 'completeJob']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Notification\NotificationController::class, 'index']);
            Route::get('count', [\App\Http\Controllers\Api\V1\Notification\NotificationController::class, 'unreadCount']);
            Route::post('{id}/read', [\App\Http\Controllers\Api\V1\Notification\NotificationController::class, 'markAsRead']);
            Route::post('read-all', [\App\Http\Controllers\Api\V1\Notification\NotificationController::class, 'markAllAsRead']);
        });

        // Reports & Export
        Route::prefix('reports')->group(function () {
            Route::get('attendance', [\App\Http\Controllers\Api\V1\Report\ReportController::class, 'attendance']);
            Route::get('tasks', [\App\Http\Controllers\Api\V1\Report\ReportController::class, 'tasks']);
            Route::get('targets', [\App\Http\Controllers\Api\V1\Report\ReportController::class, 'targets']);
            Route::get('performance', [\App\Http\Controllers\Api\V1\Report\ReportController::class, 'performance']);
            Route::post('{type}/export', [\App\Http\Controllers\Api\V1\Report\ReportController::class, 'triggerExport']);
        });

        // Employee sub-resources
        Route::prefix('employees/{employee}')->group(function () {
            // Contracts
            Route::get('contracts', [EmployeeContractController::class, 'index']);
            Route::post('contracts', [EmployeeContractController::class, 'store']);
            Route::get('contracts/{contract}', [EmployeeContractController::class, 'show']);
            Route::put('contracts/{contract}', [EmployeeContractController::class, 'update']);
            Route::delete('contracts/{contract}', [EmployeeContractController::class, 'destroy']);

            // Documents
            Route::get('documents', [EmployeeDocumentController::class, 'index']);
            Route::post('documents', [EmployeeDocumentController::class, 'store']);
            Route::get('documents/{document}/download', [EmployeeDocumentController::class, 'download']);
            Route::delete('documents/{document}', [EmployeeDocumentController::class, 'destroy']);

            // Emergency contacts
            Route::get('emergency-contacts', [EmployeeEmergencyContactController::class, 'index']);
            Route::post('emergency-contacts', [EmployeeEmergencyContactController::class, 'store']);
            Route::put('emergency-contacts/{contact}', [EmployeeEmergencyContactController::class, 'update']);
            Route::delete('emergency-contacts/{contact}', [EmployeeEmergencyContactController::class, 'destroy']);

            // History (read-only)
            Route::get('history', [EmployeeHistoryController::class, 'index']);
        });
    });

});
