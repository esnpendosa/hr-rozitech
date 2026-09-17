<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\PerformanceResult;
use App\Models\Target;
use App\Models\Task;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ReportWebController
 *
 * Pusat pelaporan dan analitik data perusahaan (Presensi, Proyek, Target, dan Rapor Kinerja).
 */
class ReportWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $stats = [
            'total_attendance' => AttendanceRecord::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count(),
            'total_tasks'      => Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count(),
            'total_targets'    => Target::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count(),
            'total_evaluated'  => PerformanceResult::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count(),
        ];

        return view('reports.index', compact('stats'));
    }
}
