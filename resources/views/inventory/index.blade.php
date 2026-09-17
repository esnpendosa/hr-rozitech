@extends('layouts.app')
@section('title', 'Inventaris & Aset')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Inventaris & Stok Aset</h1>
            <p class="text-sm text-slate-500 mt-1">Pelacakan stok suku cadang, material kerja lapangan, dan inventaris aset kantor.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Mutasi Stok</span>
            </button>
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Barang Baru</span>
            </button>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Stok Barang</h2>
            <span class="text-xs text-slate-400">Total {{ $items->total() }} barang</span>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">SKU</th>
                    <th class="px-5 py-3">Nama Barang</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3 text-center">Stok</th>
                    <th class="px-5 py-3">Satuan</th>
                    <th class="px-5 py-3 text-right">Harga Satuan</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 font-mono text-xs font-bold text-blue-600">{{ $item->sku }}</td>
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $item->name }}</td>
                        <td class="px-5 py-3 text-slate-600 text-xs">{{ $item->category?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-center font-bold {{ $item->current_stock <= $item->min_stock ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $item->current_stock }}
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $item->unit }}</td>
                        <td class="px-5 py-3 text-right text-slate-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($item->current_stock <= 0)
                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-rose-50 text-rose-700">Habis</span>
                            @elseif($item->current_stock <= $item->min_stock)
                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-amber-50 text-amber-700">Stok Kritis</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-emerald-50 text-emerald-700">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada data barang di inventaris.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
