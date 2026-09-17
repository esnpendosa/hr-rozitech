<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Locale switching
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// Web Authentication & Self-Registration
Route::get('/register', [\App\Http\Controllers\Web\AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [\App\Http\Controllers\Web\AuthWebController::class, 'processRegister'])->name('register.post');
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    return back()->withErrors(['email' => 'Kredensial yang diberikan tidak cocok dengan data kami.'])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', fn() => back()->with('status', 'Jika email Anda terdaftar, kami telah mengirimkan tautan reset kata sandi.'))->name('password.email');


// Dashboard
Route::get('/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard');
Route::get('/audit-logs', [\App\Http\Controllers\Web\AuditLogWebController::class, 'index'])->name('audit-logs.index');


// Organization
Route::get('/organization', function (\Illuminate\Http\Request $request) {
    $tenantId = \App\Support\TenantContext::getTenantId() ?? $request->user()?->tenant_id;
    $tenant = $request->user()?->tenant_id ? \App\Models\Tenant::find($tenantId) : null;
    $company = \App\Models\Company::where('tenant_id', $tenantId)->first();

    if (!$company && $tenant) {
        $company = \App\Models\Company::create([
            'tenant_id' => $tenant->id,
            'name'      => $tenant->name,
            'email'     => $request->user()?->email,
        ]);
    }

    $branchesCount = \App\Models\Branch::where('tenant_id', $tenantId)->count();
    $departmentsCount = \App\Models\Department::where('tenant_id', $tenantId)->count();
    $teamsCount = \App\Models\Team::where('tenant_id', $tenantId)->count();
    $positionsCount = \App\Models\Position::where('tenant_id', $tenantId)->count();
    $employeesCount = \App\Models\Employee::where('tenant_id', $tenantId)->count();

    return view('organization.index', compact(
        'tenant', 'company', 'branchesCount', 'departmentsCount',
        'teamsCount', 'positionsCount', 'employeesCount'
    ));
})->name('organization');

// Organization Structure Web Routes
Route::get('/branches', [\App\Http\Controllers\Web\OrganizationWebController::class, 'branches'])->name('branches.index');
Route::post('/branches', [\App\Http\Controllers\Web\OrganizationWebController::class, 'storeBranch'])->name('branches.store');
Route::delete('/branches/{id}', [\App\Http\Controllers\Web\OrganizationWebController::class, 'destroyBranch'])->name('branches.destroy');

Route::get('/departments', [\App\Http\Controllers\Web\OrganizationWebController::class, 'departments'])->name('departments.index');
Route::post('/departments', [\App\Http\Controllers\Web\OrganizationWebController::class, 'storeDepartment'])->name('departments.store');
Route::delete('/departments/{id}', [\App\Http\Controllers\Web\OrganizationWebController::class, 'destroyDepartment'])->name('departments.destroy');

Route::get('/positions', [\App\Http\Controllers\Web\OrganizationWebController::class, 'positions'])->name('positions.index');
Route::post('/positions', [\App\Http\Controllers\Web\OrganizationWebController::class, 'storePosition'])->name('positions.store');
Route::delete('/positions/{id}', [\App\Http\Controllers\Web\OrganizationWebController::class, 'destroyPosition'])->name('positions.destroy');

Route::get('/teams', [\App\Http\Controllers\Web\OrganizationWebController::class, 'teams'])->name('teams.index');
Route::post('/teams', [\App\Http\Controllers\Web\OrganizationWebController::class, 'storeTeam'])->name('teams.store');
Route::delete('/teams/{id}', [\App\Http\Controllers\Web\OrganizationWebController::class, 'destroyTeam'])->name('teams.destroy');

Route::get('/work-locations', [\App\Http\Controllers\Web\OrganizationWebController::class, 'workLocations'])->name('work-locations.index');
Route::post('/work-locations', [\App\Http\Controllers\Web\OrganizationWebController::class, 'storeWorkLocation'])->name('work-locations.store');
Route::delete('/work-locations/{id}', [\App\Http\Controllers\Web\OrganizationWebController::class, 'destroyWorkLocation'])->name('work-locations.destroy');

// Employees
Route::get('/employees', fn() => view('employees.index'))->name('employees.index');
Route::get('/employees/create', fn() => view('employees.create'))->name('employees.create');
Route::post('/employees', fn() => redirect()->route('employees.index'))->name('employees.store');
Route::get('/employees/{id}', fn($id) => view('employees.show', ['id' => $id]))->name('employees.show');
Route::get('/employees/{id}/edit', fn($id) => view('employees.create', ['id' => $id]))->name('employees.edit');
Route::delete('/employees/{id}', fn($id) => redirect()->route('employees.index'))->name('employees.destroy');

// Attendance Web Pages
Route::get('/attendance', [\App\Http\Controllers\Web\AttendanceWebController::class, 'index'])->name('attendance.index');
Route::get('/attendance/calendar', [\App\Http\Controllers\Web\AttendanceWebController::class, 'calendar'])->name('attendance.calendar');
Route::get('/attendance/leaves', [\App\Http\Controllers\Web\AttendanceWebController::class, 'leaves'])->name('attendance.leaves');
Route::get('/attendance/corrections', [\App\Http\Controllers\Web\AttendanceWebController::class, 'corrections'])->name('attendance.corrections');
Route::get('/attendance/shifts', [\App\Http\Controllers\Web\AttendanceWebController::class, 'shifts'])->name('attendance.shifts');
Route::get('/attendance/{id}', [\App\Http\Controllers\Web\AttendanceWebController::class, 'show'])->name('attendance.show');

// Fingerprint Web Pages
Route::get('/fingerprint', [\App\Http\Controllers\Web\FingerprintWebController::class, 'index'])->name('fingerprint.index');
Route::post('/fingerprint', [\App\Http\Controllers\Web\FingerprintWebController::class, 'storeDevice'])->name('fingerprint.store');
Route::delete('/fingerprint/{id}', [\App\Http\Controllers\Web\FingerprintWebController::class, 'destroyDevice'])->name('fingerprint.destroy');
Route::get('/fingerprint/sync-logs', [\App\Http\Controllers\Web\FingerprintWebController::class, 'syncLogs'])->name('fingerprint.logs');
Route::get('/fingerprint/mappings', [\App\Http\Controllers\Web\FingerprintWebController::class, 'mappings'])->name('fingerprint.mappings');

// Tasks & Projects Web Pages
Route::get('/tasks', [\App\Http\Controllers\Web\TaskWebController::class, 'index'])->name('tasks.index');
Route::get('/tasks/kanban', [\App\Http\Controllers\Web\TaskWebController::class, 'kanban'])->name('tasks.kanban');
Route::get('/tasks/{id}', [\App\Http\Controllers\Web\TaskWebController::class, 'show'])->name('tasks.show');
Route::get('/projects', [\App\Http\Controllers\Web\TaskWebController::class, 'projects'])->name('projects.index');
Route::get('/projects/{id}', [\App\Http\Controllers\Web\TaskWebController::class, 'showProject'])->name('projects.show');

// Targets Web Pages
Route::get('/targets', [\App\Http\Controllers\Web\TargetWebController::class, 'index'])->name('targets.index');
Route::post('/targets', [\App\Http\Controllers\Web\TargetWebController::class, 'store'])->name('targets.store');
Route::get('/targets/{id}', [\App\Http\Controllers\Web\TargetWebController::class, 'show'])->name('targets.show');

// KPI Web Pages
Route::get('/kpi', [\App\Http\Controllers\Web\KpiWebController::class, 'index'])->name('kpi.index');
Route::get('/kpi/templates', [\App\Http\Controllers\Web\KpiWebController::class, 'templates'])->name('kpi.templates');
Route::post('/kpi/templates', [\App\Http\Controllers\Web\KpiWebController::class, 'storeTemplate'])->name('kpi.templates.store');
Route::get('/kpi/{id}', [\App\Http\Controllers\Web\KpiWebController::class, 'show'])->name('kpi.show');

// Performance Web Pages
Route::get('/performance', [\App\Http\Controllers\Web\PerformanceWebController::class, 'index'])->name('performance.index');
Route::get('/performance/{id}', [\App\Http\Controllers\Web\PerformanceWebController::class, 'show'])->name('performance.show');

// Customer & Field Work Web Pages
Route::get('/customers', [\App\Http\Controllers\Web\CustomerWebController::class, 'index'])->name('customers.index');
Route::get('/customers/jobs', [\App\Http\Controllers\Web\CustomerWebController::class, 'jobs'])->name('customers.jobs');
Route::get('/customers/{id}', [\App\Http\Controllers\Web\CustomerWebController::class, 'show'])->name('customers.show');

// Notification Web Pages
Route::get('/notifications', [\App\Http\Controllers\Web\NotificationWebController::class, 'index'])->name('notifications.index');

// Reports Web Pages
Route::get('/reports', [\App\Http\Controllers\Web\ReportWebController::class, 'index'])->name('reports.index');

// Payroll & Keuangan (Akuntansi)
Route::get('/accounting', [\App\Http\Controllers\Web\AccountingWebController::class, 'index'])->name('accounting.index');

// Inventaris & Aset
Route::get('/inventory', [\App\Http\Controllers\Web\InventoryWebController::class, 'index'])->name('inventory.index');

// Settings & User Management
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/company', [\App\Http\Controllers\Web\SettingsWebController::class, 'company'])->name('company');
    Route::put('/company', [\App\Http\Controllers\Web\SettingsWebController::class, 'updateCompany'])->name('company.update');
    Route::get('/subscription', [\App\Http\Controllers\Web\SettingsWebController::class, 'subscription'])->name('subscription');
    Route::get('/users', [\App\Http\Controllers\Web\SettingsWebController::class, 'users'])->name('users');
    Route::post('/users', [\App\Http\Controllers\Web\SettingsWebController::class, 'storeUser'])->name('users.store');
    Route::get('/roles', [\App\Http\Controllers\Web\SettingsWebController::class, 'roles'])->name('roles');
});

// Langganan Organisasi & Layar Akun Terkunci (Lockout Screen)
Route::get('/subscription/locked', [\App\Http\Controllers\Web\SubscriptionWebController::class, 'locked'])->name('subscription.locked');
Route::post('/subscription/upgrade', [\App\Http\Controllers\Web\SubscriptionWebController::class, 'upgrade'])->name('subscription.upgrade.process');
Route::post('/subscription/simulate-expired', [\App\Http\Controllers\Web\SubscriptionWebController::class, 'simulateExpired'])->name('subscription.simulate-expired');
Route::post('/subscription/simulate-active', [\App\Http\Controllers\Web\SubscriptionWebController::class, 'simulateActive'])->name('subscription.simulate-active');

// Asisten AI RMIH (RMIH Lanjutan)
Route::get('/ai-assistant', [\App\Http\Controllers\Web\AiAssistantWebController::class, 'index'])->name('ai.index');
Route::post('/ai-assistant/chat', [\App\Http\Controllers\Web\AiAssistantWebController::class, 'chat'])->name('ai.chat');
Route::post('/ai-assistant/clear', [\App\Http\Controllers\Web\AiAssistantWebController::class, 'clearHistory'])->name('ai.clear');



