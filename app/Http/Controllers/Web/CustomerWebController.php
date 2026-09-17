<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FieldJob;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CustomerWebController
 *
 * Antarmuka web direktori pelanggan dan penugasan pekerjaan lapangan (Field Work).
 */
class CustomerWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $customers = Customer::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['assignedUser'])
            ->withCount('fieldJobs')
            ->latest()
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function show(Request $request, string $id): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $customer = Customer::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['assignedUser', 'fieldJobs.assignee.user'])
            ->findOrFail($id);

        return view('customers.show', compact('customer'));
    }

    public function jobs(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $jobs = FieldJob::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['customer', 'assignee.user'])
            ->latest('scheduled_at')
            ->paginate(15);

        return view('customers.jobs', compact('jobs'));
    }
}
