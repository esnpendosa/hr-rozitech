<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\FingerprintDevice;
use App\Models\FingerprintMapping;
use App\Models\FingerprintSyncRun;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FingerprintWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $devices = FingerprintDevice::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['branch'])
            ->withCount(['deviceUsers', 'syncRuns'])
            ->latest()
            ->paginate(15);

        $branches = Branch::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->get();

        return view('fingerprint.index', compact('devices', 'branches'));
    }

    public function syncLogs(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $logs = FingerprintSyncRun::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['device', 'errors'])
            ->latest()
            ->paginate(20);

        return view('fingerprint.sync-logs', compact('logs'));
    }

    public function mappings(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $mappings = FingerprintMapping::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['employee.user', 'device'])
            ->latest()
            ->paginate(20);

        $devices = FingerprintDevice::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->get();
        $employees = Employee::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->with('user')->get();

        return view('fingerprint.mappings', compact('mappings', 'devices', 'employees'));
    }

    public function storeDevice(Request $request)
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $validated = $request->validate([
            'branch_id'     => 'required|exists:branches,id',
            'name'          => 'required|string|max:100',
            'serial_number' => 'required|string|max:100',
            'ip_address'    => 'nullable|string|max:45',
            'port'          => 'nullable|integer|min:1|max:65535',
            'provider'      => 'nullable|string|max:50',
        ]);

        FingerprintDevice::create([
            'tenant_id'     => $tenantId,
            'branch_id'     => $validated['branch_id'],
            'name'          => $validated['name'],
            'serial_number' => $validated['serial_number'],
            'ip_address'    => $validated['ip_address'] ?? null,
            'port'          => $validated['port'] ?? 4370,
            'provider'      => $validated['provider'] ?? 'xsolutions',
            'status'        => 'active',
        ]);

        return redirect()->route('fingerprint.index')->with('success', 'Perangkat fingerprint berhasil didaftarkan.');
    }

    public function destroyDevice(Request $request, string $id)
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;
        FingerprintDevice::where('tenant_id', $tenantId)->where('id', $id)->delete();

        return redirect()->route('fingerprint.index')->with('success', 'Perangkat fingerprint berhasil dihapus.');
    }
}
