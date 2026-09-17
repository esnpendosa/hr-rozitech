@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Matriks &amp; Rapor Kinerja Karyawan</h1>
            <p class="text-sm text-gray-500 mt-1">Agregasi terpadu 4 pilar: KPI, Tugas Proyek, Target Kuantitatif, dan Disiplin Presensi</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="calculatePerformance()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Jalankan Kalkulasi Periode
            </button>
        </div>
    </div>

    <!-- Period Filter Pills -->
    @if($periods->isNotEmpty())
    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
        @foreach($periods as $p)
        <a href="{{ route('performance.index', ['period_id' => $p->id]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap {{ $selectedPeriodId === $p->id ? 'bg-primary-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            {{ $p->name }}
        </a>
        @endforeach
    </div>
    @endif

    <!-- Table Rapor -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Peringkat & Pegawai</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor KPI</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Tugas</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Target</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Presensi</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Skor Akhir</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Predikat</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($results as $index => $res)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <span class="w-6 text-center font-bold text-xs {{ $index < 3 ? 'text-primary-600' : 'text-gray-400' }}">#{{ $results->firstItem() + $index }}</span>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $res->employee?->user?->name ?? 'Pegawai' }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $res->employee?->employee_number ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-medium">{{ number_format($res->kpi_score, 1) }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-medium">{{ number_format($res->task_score, 1) }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-medium">{{ number_format($res->target_score, 1) }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-xs font-medium">{{ number_format($res->attendance_score, 1) }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center">
                            <span class="text-base font-bold text-gray-900">{{ number_format($res->final_score, 1) }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center">
                            @php
                                $gradeBadge = match($res->grade) {
                                    'A' => 'bg-green-100 text-green-800 border-green-200',
                                    'B' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'C' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    default => 'bg-red-100 text-red-800 border-red-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $gradeBadge }}">
                                Grade {{ $res->grade }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('performance.show', $res->id) }}" class="text-primary-600 hover:text-primary-900 font-medium">Rincian</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-500">
                            Belum ada hasil kalkulasi penilaian kinerja untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $results->links() }}
    </div>
</div>

<script>
function calculatePerformance() {
    const periodId = '{{ $selectedPeriodId }}';
    if (!periodId) {
        alert('Pilih periode terlebih dahulu!');
        return;
    }

    fetch('/api/v1/performance/calculate', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ period_id: periodId })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Kalkulasi selesai.');
        window.location.reload();
    })
    .catch(() => alert('Gagal menjalankan kalkulasi.'));
}
</script>
@endsection
