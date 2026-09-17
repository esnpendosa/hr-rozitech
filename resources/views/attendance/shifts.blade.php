@extends('layouts.app')
@section('title', 'Shift & Jadwal Kerja')
@section('content')

{{-- Header & Subnav --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2">
            <a href="/attendance" class="text-gray-400 hover:text-gray-600 text-sm">← {{ __('attendance.attendance') }}</a>
            <span class="text-gray-300">/</span>
            <h1 class="text-2xl font-bold text-gray-800">Shift & Jadwal Kerja</h1>
        </div>
        <p class="text-sm text-gray-500">Konfigurasi jam operasional kerja dan penugasan jadwal karyawan</p>
    </div>
</div>

<div class="space-y-8">
    {{-- Section 1: Work Shifts --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <div>
                <h2 class="font-bold text-gray-800 text-base">Daftar Shift Kerja</h2>
                <p class="text-xs text-gray-400">Master pengaturan waktu kerja per shift</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($shifts as $shift)
                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 hover:border-blue-200 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-900 text-sm">{{ $shift->name }}</h3>
                        @if($shift->is_overnight)
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Shift Malam</span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-2 font-mono text-base font-bold text-blue-700 mb-3">
                        {{ $shift->start_time }} - {{ $shift->end_time }}
                    </div>
                    <div class="text-xs text-gray-500 space-y-1">
                        <div>Toleransi Telat: <span class="font-semibold text-gray-700">{{ $shift->tolerance_late_minutes ?? 0 }} menit</span></div>
                        <div>Awal Lembur: <span class="font-semibold text-gray-700">+{{ $shift->overtime_start_minutes ?? 0 }} menit</span></div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 text-gray-400">
                    <p class="text-sm">Belum ada data shift kerja</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Section 2: Work Schedules --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <div>
                <h2 class="font-bold text-gray-800 text-base">Penugasan Jadwal Kerja</h2>
                <p class="text-xs text-gray-400">Jadwal berulang (harian/mingguan) atau penugasan tanggal spesifik</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Penerima Jadwal</th>
                        <th class="px-4 py-3">Shift Ditugaskan</th>
                        <th class="px-4 py-3">Tipe Berulang</th>
                        <th class="px-4 py-3">Hari Berlaku</th>
                        <th class="px-4 py-3">Masa Berlaku</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $sch)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                @if($sch->employee)
                                    <div class="font-medium text-gray-900">{{ $sch->employee->name }}</div>
                                    <div class="text-xs text-gray-400">Karyawan: {{ $sch->employee->employee_number }}</div>
                                @elseif($sch->team)
                                    <div class="font-medium text-blue-700">Tim: {{ $sch->team->name }}</div>
                                    <div class="text-xs text-gray-400">Seluruh anggota tim</div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="font-semibold text-gray-800">{{ $sch->shift?->name ?? '—' }}</span>
                                <span class="text-xs font-mono text-gray-500 block">{{ $sch->shift?->start_time }} - {{ $sch->shift?->end_time }}</span>
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <span class="inline-flex px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-700 capitalize">
                                    {{ $sch->recurrence_type ?? 'Spesifik' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-600">
                                @if($sch->schedule_date)
                                    <span class="font-mono">{{ \Carbon\Carbon::parse($sch->schedule_date)->format('d M Y') }}</span>
                                @elseif($sch->recurrence_type === 'weekly')
                                    <span>Senin - Jumat (Hari Kerja)</span>
                                @else
                                    <span>Setiap Hari</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-mono text-xs text-gray-500">
                                {{ $sch->valid_from ? \Carbon\Carbon::parse($sch->valid_from)->format('d/m/Y') : 'Selamanya' }}
                                @if($sch->valid_until)
                                    s/d {{ \Carbon\Carbon::parse($sch->valid_until)->format('d/m/Y') }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-400">
                                <p class="text-base font-medium">Belum ada penugasan jadwal kerja</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
