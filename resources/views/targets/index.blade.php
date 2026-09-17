@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Target Kinerja &amp; Sasaran</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau pencapaian KPI kuantitatif, laju harian yang dibutuhkan, serta level risiko ketercapaian.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="openTargetModal()"
                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Target Baru</span>
            </button>
        </div>
    </div>

    <!-- Table / Content -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Nama Target &amp; Metrik</th>
                        <th class="py-3.5 px-4 font-semibold">Periode</th>
                        <th class="py-3.5 px-4 font-semibold">Target / Capaian</th>
                        <th class="py-3.5 px-4 font-semibold">Kebutuhan Laju Harian</th>
                        <th class="py-3.5 px-4 font-semibold">Progres</th>
                        <th class="py-3.5 px-4 font-semibold">Risiko</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($targets as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4">
                            <a href="{{ route('targets.show', $t->id) }}" class="font-bold text-gray-900 hover:text-blue-600 transition">
                                {{ $t->name }}
                            </a>
                            <span class="block text-xs text-gray-400 mt-0.5">Metrik: {{ $t->metric }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600">
                            <span class="capitalize font-medium text-gray-800">{{ $t->period }}</span>
                            <span class="block text-[11px] text-gray-400">{{ $t->start_date->format('d M') }} - {{ $t->end_date->format('d M Y') }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900">{{ number_format($t->actual_value) }}</span>
                            <span class="text-xs text-gray-400">/ {{ number_format($t->target_value) }} {{ $t->unit }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                            @if($t->remaining_value > 0)
                            <span class="font-semibold text-blue-700">{{ number_format($t->required_daily_rate, 1) }} {{ $t->unit }}/hari</span>
                            @else
                            <span class="text-emerald-600 font-semibold">Tercapai</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $t->progress_percent) }}%"></div>
                                </div>
                                <span class="text-xs font-bold font-mono text-gray-700">{{ $t->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @php
                                $badge = match($t->risk_level) {
                                    'critical'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'at_risk'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'expired'   => 'bg-gray-100 text-gray-700 border-gray-200',
                                    default     => 'bg-blue-50 text-blue-700 border-blue-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $t->risk_level)) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('targets.show', $t->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">Lihat Detail &rarr;</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900">Belum ada target kinerja yang dibuat</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Tetapkan target kuantitatif bulanan atau triwulanan untuk mengukur akselerasi tim Anda.</p>
                            <button type="button" onclick="openTargetModal()"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Buat Target Pertama</span>
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $targets->links() }}
    </div>
</div>

<!-- Modal Tambah Target Baru -->
<div id="modal-target" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Buat Target Kinerja Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan sasaran kuantitatif dan rentang waktu evaluasi.</p>
            </div>
            <button type="button" onclick="closeTargetModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('targets.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Target *</label>
                <input type="text" name="name" required placeholder="Contoh: Akuisisi 50 Pelanggan Baru Q3"
                    class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Metrik *</label>
                    <input type="text" name="metric" required placeholder="Contoh: Klien Baru"
                        class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan</label>
                    <input type="text" name="unit" value="unit" placeholder="unit, orang, Rp, %"
                        class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Sasaran (Target) *</label>
                    <input type="number" step="any" name="target_value" required min="0.01" placeholder="100"
                        class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Periode *</label>
                    <select name="period" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                        <option value="monthly">Bulanan (Monthly)</option>
                        <option value="quarterly">Triwulanan (Quarterly)</option>
                        <option value="annual">Tahunan (Annual)</option>
                        <option value="weekly">Mingguan (Weekly)</option>
                        <option value="custom">Kustom</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" required value="{{ now()->startOfMonth()->toDateString() }}"
                        class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Batas Akhir (Deadline) *</label>
                    <input type="date" name="end_date" required value="{{ now()->endOfMonth()->toDateString() }}"
                        class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            @if(isset($employees) && $employees->count() > 0)
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tugaskan ke Pegawai (Opsional)</label>
                <select name="assignee_ids[]" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                    <option value="">-- Tanpa Penugasan Spesifik (Target Tim) --</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->user?->name ?? $emp->name }} ({{ $emp->employee_number ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Strategi Capaian</label>
                <textarea name="notes" rows="2" placeholder="Catatan tambahan atau strategi pelaksanaan..."
                    class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeTargetModal()" class="px-4 py-2 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Simpan Target
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modalTarget = document.getElementById('modal-target');

    function openTargetModal() {
        modalTarget.classList.remove('hidden');
    }

    function closeTargetModal() {
        modalTarget.classList.add('hidden');
    }
</script>
@endsection
