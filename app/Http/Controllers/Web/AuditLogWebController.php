<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class AuditLogWebController
 *
 * Antarmuka audit trail kepatuhan & keamanan sistem:
 * - Menampilkan histori aksi sensitif
 * - Filter aktor, aksi, tipe entitas, dan rentang tanggal
 */
class AuditLogWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $logs = AuditLog::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('entity_type'), fn($q) => $q->where('entity_type', $request->entity_type))
            ->latest('created_at')
            ->paginate(25);

        return view('audit-logs.index', compact('logs'));
    }
}
