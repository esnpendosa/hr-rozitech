@extends('layouts.app')
@section('title', __('attendance.attendance'))
@section('content')

{{-- Page Header & Module Navigation --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('attendance.attendance') }}</h1>
        <p class="text-sm text-gray-500">{{ __('attendance.summary') }} & {{ __('attendance.check_in') }} / {{ __('attendance.check_out') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="/attendance/calendar" class="bg-white border border-gray-200 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 inline-flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>{{ __('attendance.calendar') }}</span>
        </a>
        <a href="/attendance/leaves" class="bg-white border border-gray-200 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 inline-flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>{{ __('attendance.leave_submitted') }}</span>
        </a>
        <a href="/attendance/corrections" class="bg-white border border-gray-200 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 inline-flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ __('attendance.correction_submitted') }}</span>
        </a>
        <a href="/attendance/shifts" class="bg-white border border-gray-200 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 inline-flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>{{ __('attendance.shift') }} & {{ __('attendance.schedule') }}</span>
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase">{{ __('employee.employees') }}</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalEmployees }}</p>
        <p class="text-xs text-gray-400 mt-1">@lang('employee.active')</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-green-100 shadow-sm bg-green-50/30">
        <p class="text-xs font-medium text-green-700 uppercase">{{ __('attendance.check_in') }}</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $presentCount }}</p>
        <p class="text-xs text-green-600 mt-1">{{ $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100) : 0 }}% @lang('attendance.attendance_rate')</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-yellow-100 shadow-sm bg-yellow-50/30">
        <p class="text-xs font-medium text-yellow-700 uppercase">Terlambat</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $lateCount }}</p>
        <p class="text-xs text-yellow-600 mt-1">Karyawan terlambat</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-purple-100 shadow-sm bg-purple-50/30">
        <p class="text-xs font-medium text-purple-700 uppercase">Cuti & Izin</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $leaveCount }}</p>
        <p class="text-xs text-purple-600 mt-1">Disetujui hari ini</p>
    </div>
    <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm bg-red-50/30">
        <p class="text-xs font-medium text-red-700 uppercase">Belum Absen / Alpha</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $absentCount }}</p>
        <p class="text-xs text-red-600 mt-1">Belum melakukan check-in</p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="/attendance" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ request('date', $today) }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">@lang('common.search')</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau NIK karyawan..."
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">@lang('employee.status')</label>
            <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">— @lang('common.all') —</option>
                <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir (Tepat Waktu)</option>
                <option value="late"    {{ request('status') === 'late'    ? 'selected' : '' }}>Terlambat</option>
                <option value="leave"   {{ request('status') === 'leave'   ? 'selected' : '' }}>Cuti</option>
                <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                <option value="absent"  {{ request('status') === 'absent'  ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
            @lang('common.filter')
        </button>

        @if(request()->hasAny(['search', 'status']) || (request('date') && request('date') !== $today))
            <a href="/attendance" class="text-sm text-gray-500 hover:text-gray-700 self-center">
                @lang('common.reset')
            </a>
        @endif
    </form>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-4 py-3">Shift</th>
                    <th class="px-4 py-3">Jam Masuk</th>
                    <th class="px-4 py-3">Jam Keluar</th>
                    <th class="px-4 py-3">Keterlambatan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Sumber</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $rec)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $rec->employee?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $rec->employee?->employee_number }} • {{ $rec->employee?->department?->name ?? 'No Dept' }}</div>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-600">
                            {{ $rec->workSchedule?->shift?->name ?? 'Reguler' }}
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-gray-700">
                            {{ $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('H:i') : '—' }}
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-gray-700">
                            {{ $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('H:i') : '—' }}
                        </td>
                        <td class="px-4 py-4 text-xs">
                            @if($rec->late_minutes > 0)
                                <span class="text-yellow-700 font-medium">+{{ $rec->late_minutes }} mnt</span>
                            @else
                                <span class="text-gray-400">Tepat Waktu</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $badgeClasses = match($rec->status) {
                                    'present'    => 'bg-green-100 text-green-700',
                                    'late'       => 'bg-yellow-100 text-yellow-800',
                                    'leave'      => 'bg-purple-100 text-purple-700',
                                    'permission' => 'bg-indigo-100 text-indigo-700',
                                    'absent'     => 'bg-red-100 text-red-700',
                                    default      => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ ucfirst($rec->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-500 capitalize">
                            {{ $rec->source ?? 'mobile' }}
                            @if($rec->is_outside_geofence)
                                <span class="text-red-500 text-[10px] block font-medium">Luar Geofence</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="/attendance/{{ $rec->id }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <p class="text-base font-medium">Belum ada data absensi untuk tanggal ini</p>
                            <p class="text-xs text-gray-400 mt-1">Gunakan filter atau periksa jadwal shift aktif</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $records->links() }}
        </div>
    @endif
</div>

@endsection
