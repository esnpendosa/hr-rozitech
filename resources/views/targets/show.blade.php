@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('targets.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Daftar Target
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">{{ $target->name }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">Periode: {{ $target->start_date->format('d M Y') }} s/d {{ $target->end_date->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Target Keseluruhan</span>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($target->target_value) }} <span class="text-sm font-normal text-gray-500">{{ $target->unit }}</span></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Realisasi Aktual</span>
            <p class="text-2xl font-bold text-primary-600 mt-1">{{ number_format($target->actual_value) }} <span class="text-sm font-normal text-gray-500">{{ $target->unit }}</span></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Sisa Kekurangan</span>
            <p class="text-2xl font-bold text-gray-700 mt-1">{{ number_format($target->remaining_value) }} <span class="text-sm font-normal text-gray-500">{{ $target->unit }}</span></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Kebutuhan Laju Harian</span>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($target->required_daily_rate, 1) }} <span class="text-xs font-normal text-gray-500">/hari</span></p>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress History -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Riwayat Catatan Capaian (Progress Log)</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($target->progressRecords as $p)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                        <div>
                            <span class="font-bold text-sm text-gray-900">+{{ number_format($p->value) }} {{ $target->unit }}</span>
                            @if($p->notes)
                            <p class="text-xs text-gray-600 mt-0.5">{{ $p->notes }}</p>
                            @endif
                            <span class="text-[11px] text-gray-400">Dicatat oleh {{ $p->recorder?->name ?? 'User' }}</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ $p->recorded_at->format('d M Y H:i') }}</span>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-gray-400">
                        Belum ada progres aktual yang dicatat.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <!-- Assignees -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px] pb-2 border-b border-gray-100 mb-3">Penanggung Jawab</h4>
                <div class="space-y-3">
                    @forelse($target->assignments as $a)
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs">
                            {{ substr($a->employee?->user?->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-800">{{ $a->employee?->user?->name ?? 'Pegawai' }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $a->employee?->employee_number ?? '-' }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400">Belum ada penanggung jawab khusus.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
