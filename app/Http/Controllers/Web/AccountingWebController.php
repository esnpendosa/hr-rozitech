<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\PayrollRecord;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class AccountingWebController
 *
 * Mengelola modul Keuangan & Akuntansi terpadu:
 * - Bagan Akun Perkiraan (COA)
 * - Jurnal Transaksi & Buku Kas
 * - Catatan Penggajian Karyawan (Payroll)
 */
class AccountingWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $accounts = ChartOfAccount::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('code')
            ->get();

        $entries = JournalEntry::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('items.account')
            ->latest('transaction_date')
            ->paginate(15);

        $payrolls = PayrollRecord::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('employee')
            ->latest()
            ->take(10)
            ->get();

        return view('accounting.index', compact('accounts', 'entries', 'payrolls'));
    }
}
