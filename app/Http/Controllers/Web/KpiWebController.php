<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\KpiAssignment;
use App\Models\KpiMetric;
use App\Models\KpiTemplate;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class KpiWebController
 *
 * Antarmuka web penyusunan template KPI dan pelacakan capaian kinerja pegawai.
 */
class KpiWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $assignments = KpiAssignment::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['employee.user', 'template.metrics', 'results'])
            ->latest()
            ->paginate(15);

        return view('kpi.index', compact('assignments'));
    }

    public function templates(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $templates = KpiTemplate::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['metrics', 'creator'])
            ->latest()
            ->paginate(15);

        return view('kpi.templates', compact('templates'));
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;
        $userId = $request->user()?->id;

        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'description'              => 'nullable|string',
            'period'                   => 'required|in:monthly,quarterly,annual',
            'metrics'                  => 'required|array|min:1',
            'metrics.*.name'           => 'required|string|max:255',
            'metrics.*.weight'         => 'required|numeric|min:1|max:100',
            'metrics.*.target_value'   => 'required|numeric|min:0.01',
            'metrics.*.unit'           => 'nullable|string|max:50',
            'metrics.*.scoring_method' => 'required|in:higher_is_better,lower_is_better,target_based,custom',
        ]);

        $totalWeight = collect($validated['metrics'])->sum('weight');
        if (abs($totalWeight - 100) > 0.01) {
            return back()->withInput()->with('error', "Total bobot indikator harus bernilai 100%. Saat ini total bobot: {$totalWeight}%.");
        }

        DB::transaction(function () use ($validated, $tenantId, $userId) {
            $template = KpiTemplate::create([
                'tenant_id'   => $tenantId,
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'period'      => $validated['period'],
                'created_by'  => $userId,
            ]);

            foreach ($validated['metrics'] as $index => $m) {
                KpiMetric::create([
                    'tenant_id'      => $tenantId,
                    'template_id'    => $template->id,
                    'name'           => $m['name'],
                    'weight'         => $m['weight'],
                    'target_value'   => $m['target_value'],
                    'unit'           => $m['unit'] ?? '%',
                    'scoring_method' => $m['scoring_method'],
                    'sort_order'     => $index,
                ]);
            }
        });

        return redirect()->route('kpi.templates')->with('success', 'Template KPI berhasil dibuat dan siap ditugaskan ke pegawai.');
    }

    public function show(Request $request, string $id): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $assignment = KpiAssignment::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with([
                'employee.user',
                'template.metrics',
                'results.metric',
            ])
            ->findOrFail($id);

        return view('kpi.show', compact('assignment'));
    }
}
