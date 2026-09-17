@extends('layouts.app')
@section('title', 'Detail Absensi')
@section('content')

<div class="mb-6">
    <div class="flex items-center gap-2">
        <a href="/attendance" class="text-gray-400 hover:text-gray-600 text-sm">← {{ __('attendance.attendance') }}</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-2xl font-bold text-gray-800">Detail Catatan Absensi</h1>
    </div>
    <p class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($record->attendance_date)->translatedFormat('l, d F Y') }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left column: Employee & Shift --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-base mb-4 border-b pb-2">Informasi Karyawan</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-gray-400 block">Nama Lengkap</span>
                    <span class="font-semibold text-gray-800">{{ $record->employee?->name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Nomor Induk Karyawan (NIK)</span>
                    <span class="text-gray-700">{{ $record->employee?->employee_number ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Departemen & Jabatan</span>
                    <span class="text-gray-700">{{ $record->employee?->department?->name ?? '—' }} • {{ $record->employee?->position?->name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Cabang</span>
                    <span class="text-gray-700">{{ $record->employee?->branch?->name ?? 'Pusat' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-base mb-4 border-b pb-2">Jadwal & Shift</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-gray-400 block">Nama Shift</span>
                    <span class="font-semibold text-gray-800">{{ $record->workSchedule?->shift?->name ?? 'Shift Reguler' }}</span>
                </div>
                <div class="flex justify-between">
                    <div>
                        <span class="text-xs text-gray-400 block">Jam Masuk Jadwal</span>
                        <span class="font-mono text-gray-700">{{ $record->workSchedule?->shift?->start_time ?? '08:00' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Jam Keluar Jadwal</span>
                        <span class="font-mono text-gray-700">{{ $record->workSchedule?->shift?->end_time ?? '17:00' }}</span>
                    </div>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Lokasi Kerja Ditugaskan</span>
                    <span class="text-gray-700">{{ $record->workLocation?->name ?? 'Kantor Utama' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Center & Right: Attendance Details --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between border-b pb-4 mb-4">
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">Catatan Kehadiran</h2>
                    <p class="text-xs text-gray-400">ID: {{ $record->id }}</p>
                </div>
                <div>
                    @php
                        $badgeClasses = match($record->status) {
                            'present'    => 'bg-green-100 text-green-700 border-green-200',
                            'late'       => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'leave'      => 'bg-purple-100 text-purple-700 border-purple-200',
                            'permission' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                            'absent'     => 'bg-red-100 text-red-700 border-red-200',
                            default      => 'bg-gray-100 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $badgeClasses }}">
                        STATUS: {{ strtoupper($record->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <span class="text-xs text-gray-400 block">Absen Masuk</span>
                    <span class="text-lg font-bold font-mono text-gray-800">
                        {{ $record->check_in_at ? \Carbon\Carbon::parse($record->check_in_at)->format('H:i:s') : '—' }}
                    </span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <span class="text-xs text-gray-400 block">Absen Keluar</span>
                    <span class="text-lg font-bold font-mono text-gray-800">
                        {{ $record->check_out_at ? \Carbon\Carbon::parse($record->check_out_at)->format('H:i:s') : '—' }}
                    </span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <span class="text-xs text-gray-400 block">Keterlambatan</span>
                    <span class="text-lg font-bold text-gray-800 {{ $record->late_minutes > 0 ? 'text-yellow-600' : '' }}">
                        {{ $record->late_minutes ?? 0 }} mnt
                    </span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <span class="text-xs text-gray-400 block">Lembur</span>
                    <span class="text-lg font-bold text-blue-600">
                        {{ $record->overtime_minutes ?? 0 }} mnt
                    </span>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-gray-400 block">Sumber Absen</span>
                    <span class="font-medium text-gray-700 capitalize">{{ $record->source ?? 'mobile' }}</span>
                </div>

                @if($record->check_in_lat && $record->check_in_lng)
                    <div>
                        <span class="text-xs text-gray-400 block">Koordinat GPS Check-in</span>
                        <span class="font-mono text-xs text-gray-600">{{ $record->check_in_lat }}, {{ $record->check_in_lng }}</span>
                        @if($record->is_outside_geofence)
                            <span class="inline-block ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Di Luar Geofence</span>
                        @endif
                    </div>
                @endif

                @if($record->notes)
                    <div class="bg-blue-50 border border-blue-100 p-3 rounded-lg">
                        <span class="text-xs font-semibold text-blue-800 block mb-1">Catatan Sistem / Koreksi:</span>
                        <p class="text-xs text-blue-900 whitespace-pre-line">{{ $record->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Koreksi terkait --}}
        @if($record->corrections && $record->corrections->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 text-base mb-3">Riwayat Pengajuan Koreksi Absensi</h3>
                <div class="space-y-3">
                    @foreach($record->corrections as $corr)
                        <div class="border border-gray-100 rounded-lg p-3 bg-gray-50/50">
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="font-semibold text-gray-700">Status: {{ ucfirst($corr->status) }}</span>
                                <span class="text-gray-400">{{ \Carbon\Carbon::parse($corr->created_at)->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 mb-1"><span class="font-medium">Alasan:</span> {{ $corr->reason }}</p>
                            @if($corr->review_notes)
                                <p class="text-xs text-gray-500 italic"><span class="font-medium">Catatan Reviewer ({{ $corr->reviewer?->name }}):</span> {{ $corr->review_notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
