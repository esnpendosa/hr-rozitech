<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class InventoryWebController
 *
 * Mengelola modul Inventaris, Stok Barang & Aset:
 * - Daftar item barang, SKU, dan kuantitas stok
 * - Kategori produk
 * - Riwayat pergerakan / mutasi stok
 */
class InventoryWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $categories = InventoryCategory::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->get();

        $items = InventoryItem::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('category')
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->latest()
            ->paginate(15);

        $transactions = InventoryTransaction::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['item', 'employee'])
            ->latest()
            ->take(10)
            ->get();

        return view('inventory.index', compact('categories', 'items', 'transactions'));
    }
}
