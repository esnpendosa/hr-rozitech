@extends('layouts.app')
@section('title', 'Manajemen Izin & Cuti')
@section('content')

{{-- Header & Subnav --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2">
            <a href="/attendance" class="text-gray-400 hover:text-gray-600 text-sm">← {{ __('attendance.attendance') }}</a>
            <span class="text-gray-300">/</span>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Izin & Cuti</h1>
        </div>
        <p class="text-sm text-gray-500">Daftar pengajuan permohonan izin dan cuti karyawan</p>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="flex border-b border-gray-200 mb-6 space-x-4">
    <a href="/attendance/leaves" class="pb-3 text-sm font-semibold {{ !request('status') ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Semua Pengajuan
    </a>
    <a href="/attendance/leaves?status=submitted" class="pb-3 text-sm font-semibold {{ request('status') === 'submitted' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Menunggu Approval (Pending)
    </a>
    <a href="/attendance/leaves?status=approved" class="pb-3 text-sm font-semibold {{ request('status') === 'approved' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
        Disetujui
    </a>
    <a href="/attendance/leaves?status=rejected" class="pb-3 text-sm font-semibold {{ request('status') === 'rejected' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
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
                    <th class="px-4 py-3">Jenis Cuti</th>
                    <th class="px-4 py-3">Rentang Tanggal</th>
                    <th class="px-4 py-3">Total Hari</th>
                    <th class="px-4 py-3">Alasan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leaveRequests as $leave)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $leave->employee?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $leave->employee?->employee_number }} • {{ $leave->employee?->department?->name ?? 'No Dept' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                {{ $leave->leaveType?->name ?? 'Cuti' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-gray-700">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-4 text-xs font-bold text-gray-800">
                            {{ $leave->total_days }} Hari
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-600 max-w-xs truncate">
                            {{ $leave->reason ?? '—' }}
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $badgeClasses = match($leave->status) {
                                    'approved'  => 'bg-green-100 text-green-700',
                                    'submitted' => 'bg-yellow-100 text-yellow-800',
                                    'rejected'  => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($leave->status === 'submitted')
                                <div class="inline-flex items-center gap-2">
                                    <form method="POST" action="/api/v1/leaves/{{ $leave->id }}/approve" class="inline" onsubmit="event.preventDefault(); fetch(this.action, {method: 'PUT', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => location.reload());">
                                        <button type="submit" class="bg-green-600 text-white px-2.5 py-1 rounded text-xs font-medium hover:bg-green-700">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="/api/v1/leaves/{{ $leave->id }}/reject" class="inline" onsubmit="event.preventDefault(); const reason = prompt('Masukkan alasan penolakan:'); if(reason){fetch(this.action, {method: 'PUT', headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: JSON.stringify({review_notes: reason})}).then(() => location.reload());}">
                                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded text-xs font-medium hover:bg-red-100">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Diproses oleh {{ $leave->reviewer?->name ?? 'Admin' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <p class="text-base font-medium">Tidak ada permohonan izin/cuti</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leaveRequests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $leaveRequests->links() }}
        </div>
    @endif
</div>

@endsection
