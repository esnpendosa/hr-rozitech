@extends('layouts.app')
@section('title', 'Payroll & Keuangan (Akuntansi)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Payroll & Keuangan</h1>
            <p class="text-sm text-slate-500 mt-1">Pembukuan akuntansi, buku besar jurnal transaksi, dan penggajian karyawan terpadu.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Transaksi</span>
            </button>
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Proses Payroll Bulan Ini</span>
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kas & Bank</span>
            <div class="text-2xl font-bold text-slate-900 mt-2">Rp 482.500.000</div>
            <span class="text-[11px] text-emerald-600 mt-1 block">Saldo buku kas terkini</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estimasi Payroll Bulan Ini</span>
            <div class="text-2xl font-bold text-blue-600 mt-2">Rp 128.400.000</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Untuk 248 staf terdaftar</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Pembukuan</span>
            <div class="text-2xl font-bold text-emerald-600 mt-2">Seimbang (Balanced)</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Debit = Kredit terverifikasi</span>
        </div>
    </div>

    <!-- Jurnal Umum & Riwayat Transaksi -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Jurnal Transaksi Keuangan Terkini</h2>
            <span class="text-xs text-slate-400">Total {{ $entries->total() }} entri</span>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">No. Jurnal</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Keterangan</th>
                    <th class="px-5 py-3">Referensi</th>
                    <th class="px-5 py-3 text-right">Total Nilai</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($entries as $entry)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 font-mono text-xs font-bold text-blue-600">{{ $entry->entry_number }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $entry->transaction_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $entry->description }}</td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $entry->reference ?? '-' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($entry->total_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 text-xs font-medium rounded bg-emerald-50 text-emerald-700">
                                {{ strtoupper($entry->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada catatan jurnal keuangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
