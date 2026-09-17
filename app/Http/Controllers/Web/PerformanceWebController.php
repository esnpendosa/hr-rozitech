<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PerformancePeriod;
use App\Models\PerformanceResult;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class PerformanceWebController
 *
 * Antarmuka dashboard matriks kinerja, distribusi ranking pegawai, dan konfigurasi bobot.
 */
class PerformanceWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $periods = PerformancePeriod::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->latest('start_date')
            ->get();

        $selectedPeriodId = $request->get('period_id', $periods->first()?->id);

        $results = PerformanceResult::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($selectedPeriodId, fn($q) => $q->where('period_id', $selectedPeriodId))
            ->with(['employee.user', 'period'])
            ->orderByDesc('final_score')
            ->paginate(15);

        return view('performance.index', compact('periods', 'results', 'selectedPeriodId'));
    }

    public function show(Request $request, string $id): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $result = PerformanceResult::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['employee.user', 'period'])
            ->findOrFail($id);

        return view('performance.show', compact('result'));
    }
}
