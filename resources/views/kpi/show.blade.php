@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('kpi.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Daftar KPI
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">Rincian KPI: {{ $assignment->employee?->user?->name }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">Periode: {{ $assignment->period_start->format('d M Y') }} s/d {{ $assignment->period_end->format('d M Y') }} &bull; Template: {{ $assignment->template?->name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-white px-4 py-2 rounded-xl border border-gray-200 text-right">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Total Skor Terbobot</span>
                <span class="text-xl font-bold text-primary-600">{{ number_format($assignment->getTotalScore(), 1) }} <span class="text-xs text-gray-400">/ 100</span></span>
            </div>
        </div>
    </div>

    <!-- Table Metrik Breakdown -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Indikator Kinerja</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Bobot</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Target</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Realisasi Aktual</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Capaian</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Terbobot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @php
                        $resultsByMetric = $assignment->results->keyBy('metric_id');
                    @endphp
                    @foreach($assignment->template?->metrics ?? [] as $m)
                    @php
                        $res = $resultsByMetric->get($m->id);
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-gray-900">{{ $m->name }}</span>
                            @if($m->description)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $m->description }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center font-bold text-gray-700">
                            {{ $m->weight }}%
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-medium text-gray-600">
                            {{ number_format($m->target_value) }} {{ $m->unit }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center font-semibold text-gray-900">
                            {{ $res ? number_format($res->actual_value) . ' ' . $m->unit : '-' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-bold text-gray-700">
                            {{ $res ? number_format($res->score, 1) . '%' : '-' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center font-bold text-primary-600">
                            {{ $res ? number_format($res->weighted_score, 2) : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
