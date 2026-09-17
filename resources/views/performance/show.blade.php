@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('performance.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Matriks Kinerja
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">Rapor Kinerja: {{ $result->employee?->user?->name }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $result->period?->name }} &bull; Dihitung pada {{ $result->calculated_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-white px-5 py-2.5 rounded-xl border border-gray-200 text-center">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Predikat Akhir</span>
                <span class="text-2xl font-black text-primary-600">Grade {{ $result->grade }}</span>
            </div>
        </div>
    </div>

    <!-- 4 Pillars Breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between text-xs text-gray-400 font-semibold mb-2">
                <span>Pilar 1: KPI</span>
                <span>Bobot {{ $result->period?->kpi_weight }}%</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($result->kpi_score, 1) }}</p>
            <span class="text-[11px] text-gray-500">Kontribusi: {{ number_format($result->kpi_score * ($result->period?->kpi_weight / 100), 2) }} poin</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between text-xs text-gray-400 font-semibold mb-2">
                <span>Pilar 2: Tugas Proyek</span>
                <span>Bobot {{ $result->period?->task_weight }}%</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($result->task_score, 1) }}</p>
            <span class="text-[11px] text-gray-500">Kontribusi: {{ number_format($result->task_score * ($result->period?->task_weight / 100), 2) }} poin</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between text-xs text-gray-400 font-semibold mb-2">
                <span>Pilar 3: Target</span>
                <span>Bobot {{ $result->period?->target_weight }}%</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($result->target_score, 1) }}</p>
            <span class="text-[11px] text-gray-500">Kontribusi: {{ number_format($result->target_score * ($result->period?->target_weight / 100), 2) }} poin</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between text-xs text-gray-400 font-semibold mb-2">
                <span>Pilar 4: Presensi</span>
                <span>Bobot {{ $result->period?->attendance_weight }}%</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($result->attendance_score, 1) }}</p>
            <span class="text-[11px] text-gray-500">Kontribusi: {{ number_format($result->attendance_score * ($result->period?->attendance_weight / 100), 2) }} poin</span>
        </div>
    </div>
</div>
@endsection
