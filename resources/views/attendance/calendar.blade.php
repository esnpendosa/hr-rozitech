@extends('layouts.app')
@section('title', __('attendance.calendar'))
@section('content')

{{-- Header & Subnav --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2">
            <a href="/attendance" class="text-gray-400 hover:text-gray-600 text-sm">← {{ __('attendance.attendance') }}</a>
            <span class="text-gray-300">/</span>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('attendance.calendar') }}</h1>
        </div>
        <p class="text-sm text-gray-500">Rekapitulasi kehadiran bulanan per karyawan</p>
    </div>
</div>

{{-- Filter Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="/attendance/calendar" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Karyawan</label>
            <select name="employee_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $selectedEmployee?->id === $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} ({{ $emp->employee_number }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <input type="month" name="month" value="{{ $month }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
            Tampilkan Kalender
        </button>
    </form>
</div>

@if($selectedEmployee)
    {{-- Summary Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="font-bold text-gray-900 text-lg">{{ $selectedEmployee->name }}</h2>
            <p class="text-xs text-gray-500">{{ $selectedEmployee->employee_number }} • {{ $selectedEmployee->position?->name ?? 'Staf' }} • {{ $selectedEmployee->department?->name ?? 'Umum' }}</p>
        </div>
        <div class="flex gap-4 text-xs font-medium">
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span> Hadir</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-500"></span> Terlambat</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-purple-500"></span> Cuti / Izin</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400"></span> Alpha / Libur</span>
        </div>
    </div>

    {{-- Calendar Grid --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-center font-bold text-gray-800 text-lg mb-4">
            {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
        </h3>

        <div class="grid grid-cols-7 gap-2 text-center text-xs font-semibold text-gray-400 uppercase mb-2">
            <div>Sen</div>
            <div>Sel</div>
            <div>Rab</div>
            <div>Kam</div>
            <div>Jum</div>
            <div class="text-red-400">Sab</div>
            <div class="text-red-400">Min</div>
        </div>

        @php
            $firstDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
            $daysInMonth = $startOfMonth->daysInMonth;
        @endphp

        <div class="grid grid-cols-7 gap-2">
            {{-- Empty slots for previous month --}}
            @for($i = 1; $i < $firstDayOfWeek; $i++)
                <div class="h-24 bg-gray-50/50 rounded-lg border border-transparent"></div>
            @endfor

            {{-- Days of this month --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDate = $startOfMonth->copy()->day($day)->toDateString();
                    $isWeekend = $startOfMonth->copy()->day($day)->isWeekend();
                    $record = $records[$currentDate] ?? null;
                @endphp
                <div class="h-24 rounded-lg border {{ $isWeekend ? 'bg-gray-50/70 border-gray-100' : 'bg-white border-gray-100' }} p-2 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-bold {{ $isWeekend ? 'text-red-400' : 'text-gray-700' }}">{{ $day }}</span>
                        @if($record)
                            <a href="/attendance/{{ $record->id }}" class="text-[10px] text-blue-500 hover:underline">Detail</a>
                        @endif
                    </div>

                    @if($record)
                        <div class="mt-1">
                            @if($record->status === 'present')
                                <span class="block bg-green-100 text-green-700 text-[10px] font-semibold rounded px-1 py-0.5 mb-1">
                                    Hadir {{ $record->check_in_at ? \Carbon\Carbon::parse($record->check_in_at)->format('H:i') : '' }}
                                </span>
                            @elseif($record->status === 'late')
                                <span class="block bg-yellow-100 text-yellow-800 text-[10px] font-semibold rounded px-1 py-0.5 mb-1">
                                    Telat +{{ $record->late_minutes }}m
                                </span>
                            @elseif(in_array($record->status, ['leave', 'permission']))
                                <span class="block bg-purple-100 text-purple-700 text-[10px] font-semibold rounded px-1 py-0.5 mb-1">
                                    {{ ucfirst($record->status) }}
                                </span>
                            @else
                                <span class="block bg-red-100 text-red-700 text-[10px] font-semibold rounded px-1 py-0.5 mb-1">
                                    {{ ucfirst($record->status) }}
                                </span>
                            @endif

                            @if($record->check_out_at)
                                <span class="text-[9px] text-gray-400 block font-mono">
                                    out: {{ \Carbon\Carbon::parse($record->check_out_at)->format('H:i') }}
                                </span>
                            @endif
                        </div>
                    @else
                        @if(!$isWeekend && $startOfMonth->copy()->day($day)->isPast())
                            <span class="text-[10px] text-gray-300 italic self-center">Alpha</span>
                        @endif
                    @endif
                </div>
            @endfor
        </div>
    </div>
@endif

@endsection
