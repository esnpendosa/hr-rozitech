<?php

namespace App\Http\Controllers\Api\V1\Kpi;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\KpiAssignment;
use App\Models\KpiMetric;
use App\Models\KpiResult;
use App\Models\KpiTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class KpiController
 *
 * Mengelola konfigurasi template KPI, metrik berbobot, penugasan ke pegawai,
 * serta penginputan nilai aktual dan pembobotan skor akhir.
 */
class KpiController extends Controller
{
    /**
     * Menampilkan daftar template KPI.
     */
    public function getTemplates(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $templates = KpiTemplate::where('tenant_id', $tenantId)
            ->with(['metrics'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($templates);
    }

    /**
     * Membuat template KPI baru beserta metrik-metriknya (validasi total bobot = 100%).
     */
    public function storeTemplate(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

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

        // Verifikasi total bobot sama dengan 100%
        $totalWeight = collect($validated['metrics'])->sum('weight');
        if (abs($totalWeight - 100) > 0.01) {
            return ApiResponse::error("Total bobot metrik harus tepat bernilai 100%. Saat ini: {$totalWeight}%", status: 422);
        }

        $template = DB::transaction(function () use ($validated, $tenantId, $userId) {
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
                    'unit'           => $m['unit'] ?? null,
                    'scoring_method' => $m['scoring_method'],
                    'sort_order'     => $index,
                ]);
            }

            return $template;
        });

        $template->load('metrics');

        return ApiResponse::created($template, 'Template KPI berhasil dibuat');
    }

    /**
     * Menetapkan template KPI ke pegawai tertentu.
     */
    public function assignTemplate(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $validated = $request->validate([
            'template_id'  => 'required|uuid|exists:kpi_templates,id',
            'employee_id'  => 'required|uuid|exists:employees,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
        ]);

        $template = KpiTemplate::where('tenant_id', $tenantId)->findOrFail($validated['template_id']);
        Employee::where('tenant_id', $tenantId)->findOrFail($validated['employee_id']);

        $assignment = KpiAssignment::create([
            'tenant_id'        => $tenantId,
            'template_id'      => $template->id,
            'employee_id'      => $validated['employee_id'],
            'period_start'     => $validated['period_start'],
            'period_end'       => $validated['period_end'],
            'status'           => 'active',
            'template_version' => $template->version,
            'created_by'       => $userId,
        ]);

        $assignment->load(['template.metrics', 'employee.user']);

        return ApiResponse::created($assignment, 'KPI berhasil ditugaskan ke pegawai');
    }

    /**
     * Menampilkan daftar penugasan KPI beserta skor akumulatif.
     */
    public function getAssignments(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $assignments = KpiAssignment::where('tenant_id', $tenantId)
            ->with(['template.metrics', 'employee.user', 'results.metric'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($assignments);
    }

    /**
     * Memasukkan nilai aktual capaian KPI dan menghitung skor otomatis.
     */
    public function submitActuals(Request $request, string $assignmentId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $assignment = KpiAssignment::where('tenant_id', $tenantId)->with('template.metrics')->findOrFail($assignmentId);

        if ($assignment->status === 'closed') {
            return ApiResponse::error('Periode KPI ini sudah ditutup dan nilainya terkunci.', 422);
        }

        $validated = $request->validate([
            'actuals'               => 'required|array|min:1',
            'actuals.*.metric_id'   => 'required|uuid|exists:kpi_metrics,id',
            'actuals.*.actual_value'=> 'required|numeric|min:0',
            'actuals.*.notes'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($assignment, $validated, $tenantId, $userId) {
            foreach ($validated['actuals'] as $item) {
                $metric = KpiMetric::where('tenant_id', $tenantId)->findOrFail($item['metric_id']);
                $calc = $metric->calculateScore($item['actual_value']);

                KpiResult::updateOrCreate(
                    [
                        'tenant_id'     => $tenantId,
                        'assignment_id' => $assignment->id,
                        'metric_id'     => $metric->id,
                    ],
                    [
                        'actual_value'   => $item['actual_value'],
                        'score'          => $calc['score'],
                        'weighted_score' => $calc['weighted_score'],
                        'notes'          => $item['notes'] ?? null,
                        'recorded_by'    => $userId,
                    ]
                );
            }
        });

        $assignment->load(['results.metric', 'employee.user']);

        return ApiResponse::success([
            'assignment'  => $assignment,
            'total_score' => $assignment->getTotalScore(),
        ], 'Realisasi KPI berhasil dicatat dan dihitung');
    }
}
