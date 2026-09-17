@extends('layouts.app')
@section('title', 'Koreksi Absensi')
@section('content')

{{-- Header & Subnav --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2">
            <a href="/attendance" class="text-gray-400 hover:text-gray-600 text-sm">← {{ __('attendance.attendance') }}</a>
            <span class="text-gray-300">/</span>
            <h1 class="text-2xl font-bold text-gray-800">Persetujuan Koreksi Absensi</h1>
        </div>
        <p class="text-sm text-gray-500">Daftar pengajuan perbaikan jam masuk/keluar karyawan</p>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="flex border-b border-gray-200 mb-6 space-x-4">
    <a href="/attendance/corrections" class="pb-3 text-sm font-semibold {{ !request('status') ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Semua Pengajuan
    </a>
    <a href="/attendance/corrections?status=submitted" class="pb-3 text-sm font-semibold {{ request('status') === 'submitted' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Menunggu Review (Pending)
    </a>
    <a href="/attendance/corrections?status=approved" class="pb-3 text-sm font-semibold {{ request('status') === 'approved' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Disetujui
    </a>
    <a href="/attendance/corrections?status=rejected" class="pb-3 text-sm font-semibold {{ request('status') === 'rejected' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Ditolak
    </a>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-4 py-3">Tanggal Absensi</th>
                    <th class="px-4 py-3">Jam Masuk Diajukan</th>
                    <th class="px-4 py-3">Jam Keluar Diajukan</th>
                    <th class="px-4 py-3">Alasan Koreksi</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($corrections as $corr)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $corr->employee?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $corr->employee?->employee_number }} • {{ $corr->employee?->department?->name ?? 'No Dept' }}</div>
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-gray-800">
                            {{ \Carbon\Carbon::parse($corr->correction_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-gray-700">
                            {{ $corr->requested_check_in ? \Carbon\Carbon::parse($corr->requested_check_in)->format('H:i') : '—' }}
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-gray-700">
                            {{ $corr->requested_check_out ? \Carbon\Carbon::parse($corr->requested_check_out)->format('H:i') : '—' }}
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-600 max-w-xs truncate">
                            {{ $corr->reason }}
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $badgeClasses = match($corr->status) {
                                    'approved'  => 'bg-green-100 text-green-700',
                                    'submitted' => 'bg-yellow-100 text-yellow-800',
                                    'rejected'  => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ ucfirst($corr->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($corr->status === 'submitted')
                                <div class="inline-flex items-center gap-2">
                                    <form method="POST" action="/api/v1/attendance/corrections/{{ $corr->id }}/approve" class="inline" onsubmit="event.preventDefault(); fetch(this.action, {method: 'PUT', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => location.reload());">
                                        <button type="submit" class="bg-green-600 text-white px-2.5 py-1 rounded text-xs font-medium hover:bg-green-700">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="/api/v1/attendance/corrections/{{ $corr->id }}/reject" class="inline" onsubmit="event.preventDefault(); const reason = prompt('Masukkan alasan penolakan koreksi:'); if(reason){fetch(this.action, {method: 'PUT', headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: JSON.stringify({review_notes: reason})}).then(() => location.reload());}">
                                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded text-xs font-medium hover:bg-red-100">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Direview oleh {{ $corr->reviewer?->name ?? 'Admin' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <p class="text-base font-medium">Tidak ada pengajuan koreksi absensi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($corrections->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $corrections->links() }}
        </div>
    @endif
</div>

@endsection
