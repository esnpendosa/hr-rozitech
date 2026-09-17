<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Target;
use App\Models\TargetAssignment;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class TargetWebController
 *
 * Antarmuka web pemantauan target kinerja dan analisis risiko ketercapaian.
 */
class TargetWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $targets = Target::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['project', 'assignments.employee.user'])
            ->when($request->filled('period'), fn($q) => $q->where('period', $request->period))
            ->when($request->filled('risk_level'), fn($q) => $q->where('risk_level', $request->risk_level))
            ->latest()
            ->paginate(15);

        $employees = Employee::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('user')
            ->where('status', 'active')
            ->get();

        return view('targets.index', compact('targets', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;
        $userId = $request->user()?->id;

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'metric'        => 'required|string|max:100',
            'target_value'  => 'required|numeric|min:0.01',
            'unit'          => 'nullable|string|max:50',
            'period'        => 'required|in:daily,weekly,monthly,quarterly,annual,custom',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'notes'         => 'nullable|string',
            'assignee_ids'  => 'nullable|array',
            'assignee_ids.*'=> 'uuid|exists:employees,id',
        ]);

        $target = DB::transaction(function () use ($validated, $tenantId, $userId) {
            $target = Target::create([
                'tenant_id'    => $tenantId,
                'name'         => $validated['name'],
                'metric'       => $validated['metric'],
                'target_value' => $validated['target_value'],
                'unit'         => $validated['unit'] ?? 'unit',
                'period'       => $validated['period'],
                'start_date'   => $validated['start_date'],
                'end_date'     => $validated['end_date'],
                'notes'        => $validated['notes'] ?? null,
                'created_by'   => $userId,
            ]);

            if (!empty($validated['assignee_ids'])) {
                foreach ($validated['assignee_ids'] as $empId) {
                    TargetAssignment::create([
                        'tenant_id'     => $tenantId,
                        'target_id'     => $target->id,
                        'assignee_type' => 'employee',
                        'employee_id'   => $empId,
                        'created_at'    => now(),
                    ]);
                }
            }

            $target->recalculateMetrics();

            return $target;
        });

        return redirect()->route('targets.index')->with('success', 'Target kinerja berhasil dibuat.');
    }

    public function show(Request $request, string $id): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $target = Target::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with([
                'project',
                'assignments.employee.user',
                'progressRecords.recorder',
            ])
            ->findOrFail($id);

        return view('targets.show', compact('target'));
    }
}
