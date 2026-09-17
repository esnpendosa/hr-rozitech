<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\FingerprintDevice;
use App\Models\LeaveRequest;
use App\Models\Target;
use App\Models\Task;
use App\Models\Tenant;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class DashboardController
 *
 * Mengelola dasbor utama operasional:
 * - Menghitung ringkasan kehadiran hari ini (live real-time data)
 * - Metrik tugas aktif, terlambat, dan target
 * - Multi-tenant isolation: akun baru menampilkan kondisi riil (kosong 0)
 *   disertai panduan memulai cepat (onboarding)
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan dasbor operasional
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tenantId = TenantContext::getTenantId() ?? $user?->tenant_id;
        $tenant = $tenantId ? Tenant::find($tenantId) : null;
        $today = Carbon::today()->toDateString();

        // 1. Data Peran Pengguna (Role Badge)
        $roleName = $user?->roles?->first()?->name ?? 'owner';
        $roleLabels = [
            'super_admin' => 'Super Admin Platform',
            'owner'       => 'Owner / Direktur',
            'hr_admin'    => 'HR Admin',
            'manager'     => 'Manajer Operasional',
            'employee'    => 'Karyawan',
        ];
        $roleLabel = $roleLabels[$roleName] ?? ucfirst($roleName);

        // 2. Metrik Karyawan
        $totalEmployees = Employee::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'active')
            ->count();

        // 3. Metrik Kehadiran Hari Ini
        $presentToday = AttendanceRecord::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('attendance_date', $today)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $lateToday = AttendanceRecord::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('attendance_date', $today)
            ->where('status', 'late')
            ->count();

        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;
        $absentToday = max(0, $totalEmployees - $presentToday);

        // 4. Metrik Tugas & Proyek
        $activeTasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->count();

        $completedTasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'completed')
            ->count();

        $inProgressTasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'in_progress')
            ->count();

        $todoTasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'todo')
            ->count();

        $overdueTasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->where('due_at', '<', Carbon::now())
            ->count();

        // 5. Metrik Target & Risiko
        $totalTargets = Target::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count();
        $completedTargets = Target::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'completed')
            ->count();
        $targetProgressPercent = $totalTargets > 0 ? round(($completedTargets / $totalTargets) * 100) : 0;

        $targetsAtRisk = Target::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', ['active'])
            ->whereIn('risk_level', ['at_risk', 'critical'])
            ->count();

        // 6. Persetujuan Tertunda (Cuti/Izin)
        $pendingApprovals = LeaveRequest::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'pending')
            ->count();

        // 7. Mesin Absensi X Solutions
        $devices = FingerprintDevice::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->get();
        $totalDevices = $devices->count();
        $onlineDevices = $devices->where('status', 'online')->count();
        $offlineDevices = $totalDevices - $onlineDevices;

        // 8. Daftar Cabang Riil
        $branches = Branch::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->get();

        // 9. Tugas Terkini
        $recentTasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['project', 'assignees.employee'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'tenant',
            'roleName',
            'roleLabel',
            'totalEmployees',
            'presentToday',
            'lateToday',
            'absentToday',
            'attendanceRate',
            'activeTasks',
            'completedTasks',
            'inProgressTasks',
            'todoTasks',
            'overdueTasks',
            'totalTargets',
            'completedTargets',
            'targetProgressPercent',
            'targetsAtRisk',
            'pendingApprovals',
            'totalDevices',
            'onlineDevices',
            'offlineDevices',
            'branches',
            'recentTasks'
        ));
    }
}
