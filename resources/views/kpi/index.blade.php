@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Evaluasi Kinerja &amp; KPI Pegawai</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau realisasi skor indikator kinerja kunci berbasis bobot objektif</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('kpi.templates') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Kelola Template KPI
            </a>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tugaskan KPI Baru
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Pegawai</th>
                        <th class="py-3.5 px-4 font-semibold">Template KPI</th>
                        <th class="py-3.5 px-4 font-semibold">Periode Evaluasi</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Metrik Terisi</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Akhir KPI</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($assignments as $a)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $a->employee?->user?->name ?? 'Pegawai' }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $a->employee?->employee_number ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-800">{{ $a->template?->name ?? 'Custom KPI' }}</span>
                            <span class="block text-xs text-gray-400">{{ $a->template?->metrics->count() ?? 0 }} Indikator</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600">
                            {{ $a->period_start->format('d M Y') }} s/d {{ $a->period_end->format('d M Y') }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-medium">
                            {{ $a->results->count() }} / {{ $a->template?->metrics->count() ?? 0 }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center">
                            @php
                                $score = $a->getTotalScore();
                                $scoreClass = $score >= 85 ? 'text-green-600' : ($score >= 70 ? 'text-blue-600' : 'text-orange-600');
                            @endphp
                            <span class="text-base font-bold {{ $scoreClass }}">{{ number_format($score, 1) }}</span>
                            <span class="text-xs text-gray-400">/ 100</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $a->status === 'closed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('kpi.show', $a->id) }}" class="text-primary-600 hover:text-primary-900 font-medium">Rincian</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-500">
                            Belum ada penugasan KPI untuk periode berjalan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
