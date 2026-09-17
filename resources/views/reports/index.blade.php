@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pusat Analitik &amp; Laporan Perusahaan</h1>
            <p class="text-sm text-gray-500 mt-1">Ekspor dan visualisasikan ringkasan data SDM, operasional, target, dan performa tim</p>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Attendance Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="p-2.5 bg-blue-50 text-blue-700 rounded-lg inline-block mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Laporan Presensi</h3>
                <p class="text-xs text-gray-500 mt-1">Rekap data kehadiran, keterlambatan, lembur, cuti dan izin bulanan.</p>
                <div class="mt-4 text-xs font-semibold text-gray-700">
                    {{ number_format($stats['total_attendance']) }} Data Tercatat
                </div>
            </div>
            <div class="mt-5 pt-3 border-t border-gray-100">
                <button onclick="triggerExport('attendance')" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold">
                    Ekspor Excel
                </button>
            </div>
        </div>

        <!-- Tasks Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="p-2.5 bg-green-50 text-green-700 rounded-lg inline-block mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Laporan Tugas Proyek</h3>
                <p class="text-xs text-gray-500 mt-1">Rasio penyelesaian tugas, keterlambatan, dan distribusi beban kerja.</p>
                <div class="mt-4 text-xs font-semibold text-gray-700">
                    {{ number_format($stats['total_tasks']) }} Tugas Aktif & Selesai
                </div>
            </div>
            <div class="mt-5 pt-3 border-t border-gray-100">
                <button onclick="triggerExport('tasks')" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold">
                    Ekspor Excel
                </button>
            </div>
        </div>

        <!-- Targets Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="p-2.5 bg-purple-50 text-purple-700 rounded-lg inline-block mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Laporan Target Kinerja</h3>
                <p class="text-xs text-gray-500 mt-1">Persentase capaian kuantitatif dan analisis sebaran risiko target.</p>
                <div class="mt-4 text-xs font-semibold text-gray-700">
                    {{ number_format($stats['total_targets']) }} Sasaran Bisnis
                </div>
            </div>
            <div class="mt-5 pt-3 border-t border-gray-100">
                <button onclick="triggerExport('targets')" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold">
                    Ekspor Excel
                </button>
            </div>
        </div>

        <!-- Performance Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="p-2.5 bg-orange-50 text-orange-700 rounded-lg inline-block mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Laporan Matriks Kinerja</h3>
                <p class="text-xs text-gray-500 mt-1">Rapor skor akumulatif 4 pilar, pemeringkatan, dan distribusi grade.</p>
                <div class="mt-4 text-xs font-semibold text-gray-700">
                    {{ number_format($stats['total_evaluated']) }} Hasil Penilaian
                </div>
            </div>
            <div class="mt-5 pt-3 border-t border-gray-100">
                <button onclick="triggerExport('performance')" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold">
                    Ekspor Excel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function triggerExport(type) {
    fetch(`/api/v1/reports/${type}/export`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ format: 'xlsx' })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Antrian ekspor laporan telah dijadwalkan!');
    })
    .catch(() => alert('Gagal menjadwalkan ekspor laporan.'));
}
</script>
@endsection
