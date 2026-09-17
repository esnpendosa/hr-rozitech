@extends('layouts.app')

@section('title', 'Asisten RMIH')

@push('styles')
<style>
    .assistant-body h3 {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.75rem;
        margin-bottom: 0.375rem;
    }
    .assistant-body h3:first-child {
        margin-top: 0;
    }
    .assistant-body p {
        margin-bottom: 0.5rem;
        line-height: 1.6;
        color: #334155;
    }
    .assistant-body ul {
        list-style-type: disc;
        margin-left: 1.25rem;
        margin-bottom: 0.5rem;
        line-height: 1.6;
        color: #334155;
    }
    .assistant-body ol {
        list-style-type: decimal;
        margin-left: 1.25rem;
        margin-bottom: 0.5rem;
        line-height: 1.6;
        color: #334155;
    }
    .assistant-body li {
        margin-bottom: 0.25rem;
    }
    .assistant-body strong {
        font-weight: 600;
        color: #0f172a;
    }
    .assistant-body code {
        font-size: 0.75rem;
        background-color: #f1f5f9;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        color: #0f172a;
        font-family: ui-monospace, SFMono-Regular, monospace;
    }
    .assistant-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 0.75rem 0;
        font-size: 0.75rem;
    }
    .assistant-body th, .assistant-body td {
        border: 1px solid #e2e8f0;
        padding: 0.375rem 0.5rem;
        text-align: left;
    }
    .assistant-body th {
        background-color: #f8fafc;
        font-weight: 600;
        color: #0f172a;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">
                    Bantuan Operasional &amp; SDM
                </span>
                <span class="text-xs text-slate-400 font-medium">&bull; {{ $tenant?->name ?? 'Organisasi' }}</span>
                <span class="sr-only">RMIH Lanjutan Asisten AI Enterprise Konteks Live Workspace</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">
                Pusat Asistensi &amp; Data Terpadu
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Konsultasi data kehadiran real-time, status integrasi mesin absensi, simulasi payroll, dan regulasi ketenagakerjaan.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('ai.clear') }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Bersihkan riwayat percakapan saat ini?')"
                    class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Bersihkan Riwayat</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- KOLOM KIRI: Ringkasan Data & Topik Bantuan -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Snapshot Data Ringkas -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ringkasan Organisasi</h2>
                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Data Terhubung"></span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Organisasi:</span>
                        <span class="font-bold text-slate-900">{{ $context['tenant_name'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Karyawan:</span>
                        <span class="font-bold text-slate-900">{{ $context['total_employees'] }} Orang</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Hadir Hari Ini:</span>
                        <span class="font-bold text-emerald-600">{{ $context['present_today'] }} Orang</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Izin / Cuti:</span>
                        <span class="font-bold text-amber-600">{{ $context['leaves_today'] }} Orang</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Mesin X Solutions:</span>
                        <span class="font-bold text-slate-900">{{ $context['online_devices'] }} / {{ $context['total_devices'] }} Online</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Inventaris:</span>
                        <span class="font-bold text-slate-900">{{ $context['total_assets'] }} Item</span>
                    </div>
                </div>
            </div>

            <!-- Topik Pertanyaan Cepat -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Topik Sering Ditanyakan</h2>
                <div class="space-y-2">
                    @foreach($quickPrompts as $qp)
                        <button type="button" onclick="useQuickPrompt('{{ addslashes($qp) }}')"
                            class="w-full text-left p-2.5 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/40 text-xs text-slate-700 transition flex items-start gap-2 group">
                            <span class="w-4 h-4 rounded-md bg-slate-100 group-hover:bg-blue-600 group-hover:text-white text-slate-400 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5 transition">
                                &rarr;
                            </span>
                            <span class="leading-relaxed">{{ $qp }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Panduan Referensi Regulasi -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-[11px] text-slate-500 leading-relaxed space-y-2">
                <p class="font-semibold text-slate-700">Dasar Regulasi &amp; Integrasi:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Database kehadiran harian &amp; jadwal shift kerja</li>
                    <li>ADMS Push Protocol Mesin Absensi X Solutions</li>
                    <li>PP No. 35/2021 (Ketentuan Upah Lembur)</li>
                    <li>PP No. 58/2023 &amp; PMK 168/2023 (PPh 21 Tarif TER)</li>
                </ul>
            </div>

        </div>

        <!-- KOLOM KANAN: Percakapan & Input -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-2xs flex flex-col h-[700px]">
            
            <!-- Area Percakapan (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-5" id="chat-box">
                
                @if(empty($messages))
                    <!-- Sambutan Awal -->
                    <div class="py-14 text-center max-w-md mx-auto space-y-4">
                        <div class="w-12 h-12 mx-auto rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Halo, {{ auth()->user()->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Ada yang ingin ditanyakan seputar data kehadiran, panduan Mesin Absensi X Solutions, aturan lembur, atau simulasi PPh 21 TER?
                            </p>
                        </div>

                        <div class="pt-2 text-left space-y-2">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block text-center">Pilihan pertanyaan cepat:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <button type="button" onclick="useQuickPrompt('Berapa total karyawan yang hadir hari ini?')" class="p-3 border border-slate-200 rounded-xl text-xs text-slate-700 hover:border-blue-500 hover:bg-blue-50/50 transition text-left">
                                    <strong class="text-slate-900">Kehadiran Hari Ini</strong>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Cek jumlah hadir dan ketidakhadiran.</p>
                                </button>
                                <button type="button" onclick="useQuickPrompt('Bagaimana status koneksi Mesin Absensi X Solutions?')" class="p-3 border border-slate-200 rounded-xl text-xs text-slate-700 hover:border-blue-500 hover:bg-blue-50/50 transition text-left">
                                    <strong class="text-slate-900">Mesin X Solutions</strong>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Cek status online dan konfigurasi ADMS.</p>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Percakapan -->
                    @foreach($messages as $msg)
                        @if($msg['role'] === 'user')
                            <div class="flex justify-end">
                                <div class="max-w-xl bg-blue-600 text-white rounded-2xl rounded-tr-xs px-4 py-3 text-xs leading-relaxed shadow-xs">
                                    <div class="font-semibold text-[10px] text-blue-200 mb-1 flex items-center justify-between gap-4">
                                        <span>{{ auth()->user()->name }}</span>
                                        <span>{{ $msg['created_at'] ?? '' }} WIB</span>
                                    </div>
                                    <p class="whitespace-pre-wrap">{{ $msg['content'] }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="max-w-2xl bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl rounded-tl-xs px-5 py-4 text-xs leading-relaxed shadow-2xs space-y-2">
                                    <div class="flex items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
                                        <div class="flex items-center gap-2">
                                            <div class="w-5 h-5 rounded-md bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold">R</div>
                                            <span class="font-semibold text-xs text-slate-800">Asisten RMIH</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">{{ $msg['created_at'] ?? '' }} WIB</span>
                                    </div>
                                    <div class="assistant-body text-xs leading-relaxed text-slate-700">
                                        {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

            </div>

            <!-- Formulir Input Chat -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/50 rounded-b-2xl">
                <form action="{{ route('ai.chat') }}" method="POST" id="chat-form" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1 relative">
                        <textarea name="prompt" id="prompt-input" rows="2" required placeholder="Tanyakan seputar data karyawan, Mesin Absensi X Solutions, payroll, kehadiran, atau regulasi..."
                            class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition resize-none bg-white"></textarea>
                    </div>

                    <button type="submit" id="btn-submit"
                        class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2 shrink-0">
                        <span>Kirim</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
                <div class="text-[11px] text-slate-400 mt-2 px-1">
                    <span>Tekan <kbd class="px-1 py-0.5 bg-slate-200 text-slate-700 rounded text-[10px]">Enter</kbd> untuk mengirimkan pesan.</span>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    const chatBox = document.getElementById('chat-box');
    const promptInput = document.getElementById('prompt-input');
    const chatForm = document.getElementById('chat-form');
    const btnSubmit = document.getElementById('btn-submit');

    // Scroll chat ke bawah otomatis
    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    scrollToBottom();

    @if(request('q'))
        promptInput.value = @json(request('q'));
        promptInput.focus();
    @endif

    // Gunakan Quick Prompt
    function useQuickPrompt(text) {
        promptInput.value = text;
        promptInput.focus();
    }

    // Submit saat tekan Enter (kecuali Shift + Enter)
    promptInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (promptInput.value.trim() !== '') {
                submitForm();
            }
        }
    });

    function submitForm() {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span>Memproses...</span>';
        chatForm.submit();
    }
</script>
@endsection
