@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('kpi.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Daftar KPI Pegawai
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">Template Standar KPI</h1>
            <p class="text-sm text-gray-500 mt-0.5">Definisikan metrik kinerja objektif dengan bobot terstandarisasi 100%</p>
        </div>
        <div>
            <button type="button" onclick="openTemplateModal(false)"
                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Template KPI</span>
            </button>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $t)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 capitalize">
                            Periode {{ $t->period === 'monthly' ? 'Bulanan' : ($t->period === 'quarterly' ? 'Triwulanan' : 'Tahunan') }}
                        </span>
                        <h3 class="text-base font-bold text-gray-900 mt-2">{{ $t->name }}</h3>
                        <p class="text-xs text-gray-400">Versi {{ $t->version }}</p>
                    </div>
                </div>

                @if($t->description)
                <p class="text-xs text-gray-600 mt-2 line-clamp-2">{{ $t->description }}</p>
                @endif

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Metrik &amp; Bobot</h4>
                    <div class="space-y-1.5 text-xs">
                        @foreach($t->metrics as $m)
                        <div class="flex justify-between items-center py-0.5">
                            <span class="text-gray-600 truncate pr-2">{{ $m->name }}</span>
                            <span class="font-bold text-gray-900 font-mono">{{ $m->weight }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span>Dibuat oleh {{ $t->creator?->name ?? 'Admin' }}</span>
                <span class="font-semibold text-emerald-600">Aktif</span>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-200 shadow-2xs">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Belum ada template KPI</h3>
            <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Buat template KPI divisi atau gunakan indikator standar performa untuk mulai menilai pencapaian tim.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <button type="button" onclick="openTemplateModal(true)"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Gunakan Template Standar (Rekomendasi)</span>
                </button>
                <button type="button" onclick="openTemplateModal(false)"
                    class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl transition gap-1.5">
                    Buat Template Kustom
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $templates->links() }}
    </div>
</div>

<!-- Modal Tambah Template KPI -->
<div id="modal-template" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-2xl w-full p-6 space-y-5 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tambah Template Standar KPI</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan indikator penilaian dengan total bobot akumulasi 100%.</p>
            </div>
            <button type="button" onclick="closeTemplateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('kpi.templates.store') }}" method="POST" id="form-template" class="space-y-4" onsubmit="return validateTotalWeight()">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Template KPI *</label>
                    <input type="text" name="name" id="tpl-name" required placeholder="Contoh: KPI Standar Staf Operasional"
                        class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Periode Penilaian *</label>
                    <select name="period" id="tpl-period" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                        <option value="monthly">Bulanan (Monthly)</option>
                        <option value="quarterly">Triwulanan (Quarterly)</option>
                        <option value="annual">Tahunan (Annual)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Deskripsi (Opsional)</label>
                <textarea name="description" id="tpl-desc" rows="2" placeholder="Tujuan atau panduan penilaian KPI..."
                    class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
            </div>

            <!-- Daftar Indikator -->
            <div class="border-t border-slate-100 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Indikator &amp; Bobot Penilaian</h4>
                        <p class="text-[11px] text-slate-400">Total seluruh bobot harus tepat 100%.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="loadDefaultMetrics()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">
                            Isi Standar (50/25/25)
                        </button>
                        <button type="button" onclick="addMetricRow()" class="text-[11px] font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg flex items-center gap-1">
                            <span>+</span> Tambah Baris
                        </button>
                    </div>
                </div>

                <div class="space-y-2 max-h-64 overflow-y-auto pr-1" id="metrics-container">
                    <!-- Dynamic rows will be inserted here -->
                </div>

                <!-- Live Total Weight Counter -->
                <div class="mt-3 p-2.5 rounded-xl border flex items-center justify-between text-xs" id="weight-counter-box">
                    <span class="font-medium text-slate-600">Total Akumulasi Bobot:</span>
                    <span class="font-bold text-sm" id="weight-total-label">0%</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeTemplateModal()" class="px-4 py-2 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" id="btn-save-template" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Simpan Template KPI
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-template');
    const container = document.getElementById('metrics-container');
    const weightTotalLabel = document.getElementById('weight-total-label');
    const weightBox = document.getElementById('weight-counter-box');
    const btnSave = document.getElementById('btn-save-template');

    let metricIndex = 0;

    function openTemplateModal(withDefault = false) {
        modal.classList.remove('hidden');
        if (withDefault || container.children.length === 0) {
            loadDefaultMetrics();
        }
    }

    function closeTemplateModal() {
        modal.classList.add('hidden');
    }

    function addMetricRow(name = '', weight = 0, target = 100, unit = '%', method = 'higher_is_better') {
        const i = metricIndex++;
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 items-center p-2 rounded-xl bg-slate-50 border border-slate-200 text-xs metric-row';
        row.innerHTML = `
            <div class="col-span-5">
                <input type="text" name="metrics[${i}][name]" value="${escapeHtml(name)}" required placeholder="Nama Indikator"
                    class="w-full text-xs p-1.5 border border-slate-300 rounded-lg focus:outline-none bg-white">
            </div>
            <div class="col-span-2">
                <div class="relative">
                    <input type="number" name="metrics[${i}][weight]" value="${weight}" required min="1" max="100" placeholder="Bobot"
                        oninput="recalculateWeight()"
                        class="w-full text-xs p-1.5 pr-5 border border-slate-300 rounded-lg focus:outline-none bg-white font-mono metric-weight">
                    <span class="absolute right-1.5 top-1.5 text-slate-400 text-[10px]">%</span>
                </div>
            </div>
            <div class="col-span-2">
                <input type="number" step="any" name="metrics[${i}][target_value]" value="${target}" required min="0.01" placeholder="Target"
                    class="w-full text-xs p-1.5 border border-slate-300 rounded-lg focus:outline-none bg-white font-mono">
            </div>
            <div class="col-span-2">
                <select name="metrics[${i}][scoring_method]" class="w-full text-[11px] p-1.5 border border-slate-300 rounded-lg focus:outline-none bg-white">
                    <option value="higher_is_better" ${method === 'higher_is_better' ? 'selected' : ''}>Makin Tinggi Baik</option>
                    <option value="target_based" ${method === 'target_based' ? 'selected' : ''}>Target Persis</option>
                    <option value="lower_is_better" ${method === 'lower_is_better' ? 'selected' : ''}>Makin Rendah Baik</option>
                </select>
                <input type="hidden" name="metrics[${i}][unit]" value="${escapeHtml(unit)}">
            </div>
            <div class="col-span-1 text-center">
                <button type="button" onclick="removeMetricRow(this)" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        recalculateWeight();
    }

    function removeMetricRow(btn) {
        const row = btn.closest('.metric-row');
        if (container.children.length <= 1) {
            alert('Minimal harus ada 1 indikator penilaian.');
            return;
        }
        row.remove();
        recalculateWeight();
    }

    function loadDefaultMetrics() {
        container.innerHTML = '';
        document.getElementById('tpl-name').value = 'KPI Standar Staf & Operasional';
        document.getElementById('tpl-desc').value = 'Template standar penilaian pencapaian target, kedisiplinan presensi, dan kerja sama tim.';
        
        addMetricRow('Capaian Target Kerja & Proyek', 50, 100, '%', 'higher_is_better');
        addMetricRow('Kedisiplinan & Presensi (Mesin Absensi)', 25, 100, '%', 'higher_is_better');
        addMetricRow('Kerjasama Tim & Inisiatif', 25, 100, '%', 'higher_is_better');
    }

    function recalculateWeight() {
        const inputs = container.querySelectorAll('.metric-weight');
        let total = 0;
        inputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            total += val;
        });

        weightTotalLabel.textContent = `${total}%`;
        if (Math.abs(total - 100) < 0.01) {
            weightBox.className = 'mt-3 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 flex items-center justify-between text-xs font-semibold';
            weightTotalLabel.textContent = '100% (Sesuai Standar)';
            btnSave.disabled = false;
            btnSave.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            weightBox.className = 'mt-3 p-2.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 flex items-center justify-between text-xs font-medium';
            weightTotalLabel.textContent = `${total}% (Harus tepat 100%)`;
        }
    }

    function validateTotalWeight() {
        const inputs = container.querySelectorAll('.metric-weight');
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        if (Math.abs(total - 100) > 0.01) {
            alert(`Total bobot semua indikator harus berjumlah tepat 100%. Saat ini total bobot Anda: ${total}%.`);
            return false;
        }
        return true;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection
