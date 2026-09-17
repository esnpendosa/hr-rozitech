<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Akun Terkunci - RMIH Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { DEFAULT: '#1E3A8A', dark: '#0F172A' },
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    <!-- Header / Navbar Minimalis -->
    <header class="bg-white border-b border-slate-200 py-3.5 px-6 sticky top-0 z-30 shadow-2xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/images/rmih-logo.png" alt="RMIH Platform" class="h-8 w-auto">
                <div class="hidden sm:block border-l border-slate-200 pl-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pusat Langganan Organisasi</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs text-slate-600 hidden md:inline">
                    Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ $tenant->name ?? 'Organisasi' }})
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Konten Utama Lockout -->
    <main class="flex-1 py-10 px-4 sm:px-6">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Banner Status Terkunci -->
            <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-xs">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-bold uppercase tracking-wider mb-1">
                            Akses Organisasi Terkunci
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            Masa Uji Coba / Akses Layanan Telah Berakhir
                        </h1>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                            Organisasi <strong>{{ $tenant->name ?? 'Anda' }}</strong> telah menyelesaikan masa uji coba 14 hari atau masa paket aktif sebelumnya telah habis. Seluruh data operasional, riwayat presensi Mesin Absensi X Solutions, dan catatan payroll tetap aman tersimpan. Silakan pilih paket di bawah untuk mengaktifkan kembali akses penuh.
                        </p>
                    </div>
                </div>

                <div class="shrink-0 text-left sm:text-right border-t sm:border-t-0 sm:border-l border-red-200 pt-4 sm:pt-0 sm:pl-6 w-full sm:w-auto">
                    <span class="text-xs text-slate-500 block">Status Terakhir:</span>
                    <span class="text-sm font-bold text-red-700 capitalize block">
                        {{ $subscription?->status ?? 'Expired' }} (Terkunci)
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-0.5">
                        Berakhir: {{ $subscription?->ends_at ? $subscription->ends_at->translatedFormat('d M Y') : 'Hari ini' }}
                    </span>
                </div>
            </div>

            <!-- Formulir Pilihan Paket & Pembayaran / Aktivasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Pilih Paket untuk Membuka Kunci</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Semua paket dilengkapi integrasi mesin absensi biometrik X Solutions dan akuntansi.</p>
                    </div>

                    <!-- Switch Siklus Penagihan -->
                    <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs self-start sm:self-auto">
                        <button type="button" onclick="setBillingCycle('monthly')" id="tab-monthly"
                            class="px-4 py-1.5 font-semibold rounded-lg transition text-slate-600">
                            Bulanan
                        </button>
                        <button type="button" onclick="setBillingCycle('annual')" id="tab-annual"
                            class="px-4 py-1.5 font-semibold rounded-lg transition bg-white text-slate-900 shadow-2xs flex items-center gap-1.5">
                            <span>Tahunan</span>
                            <span class="text-[9px] px-1.5 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded">Hemat 17%</span>
                        </button>
                    </div>
                </div>

                <form action="{{ route('subscription.upgrade.process') }}" method="POST" id="upgrade-form" class="mt-6">
                    @csrf
                    <input type="hidden" name="plan_slug" id="selected-plan-input" value="operations">
                    <input type="hidden" name="billing_cycle" id="selected-cycle-input" value="annual">

                    <!-- Kartu 3 Paket -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        
                        <!-- Starter Plan -->
                        <div onclick="selectPlan('starter')" id="card-starter"
                            class="plan-card cursor-pointer p-5 rounded-xl border-2 transition relative flex flex-col justify-between border-slate-200 hover:border-slate-300">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-base font-bold text-slate-900">Starter</h3>
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-starter border-slate-300">
                                        <svg class="w-3 h-3 check-starter hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mb-4">Cocok untuk bisnis kecil dan klinik mandiri.</p>
                                <div class="mb-4">
                                    <div class="text-2xl font-extrabold text-slate-900 price-starter-display">Rp 249.000</div>
                                    <span class="text-xs text-slate-400 cycle-starter-display">/bulan (ditagih tahunan)</span>
                                </div>
                                <ul class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Hingga 25 Karyawan</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>1 Cabang / Unit Operasional</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Mesin Absensi X Solutions</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Presensi Mobile GPS</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Operations Plan (Default Selected & Paling Populer) -->
                        <div onclick="selectPlan('operations')" id="card-operations"
                            class="plan-card cursor-pointer p-5 rounded-xl border-2 transition relative flex flex-col justify-between border-blue-600 bg-blue-50/40 ring-2 ring-blue-100">
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-blue-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider shadow-2xs">
                                Paling Direkomendasikan
                            </span>
                            <div>
                                <div class="flex items-center justify-between mb-3 mt-1">
                                    <h3 class="text-base font-bold text-slate-900">Operations</h3>
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-operations border-blue-600 bg-blue-600 text-white">
                                        <svg class="w-3 h-3 check-operations" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mb-4">Solusi terlengkap untuk perusahaan bertumbuh.</p>
                                <div class="mb-4">
                                    <div class="text-2xl font-extrabold text-blue-600 price-operations-display">Rp 749.000</div>
                                    <span class="text-xs text-slate-400 cycle-operations-display">/bulan (ditagih tahunan)</span>
                                </div>
                                <ul class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span><strong>Hingga 150 Karyawan</strong></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Multi-Shift Kerja Fleksibel</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Auto-Push Mesin Absensi X Solutions</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Modul Payroll, PPh 21 & Inventaris</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-blue-600 font-bold">&check;</span>
                                        <span>Customer & Pelayanan CRM</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Enterprise Plan -->
                        <div onclick="selectPlan('enterprise')" id="card-enterprise"
                            class="plan-card cursor-pointer p-5 rounded-xl border-2 transition relative flex flex-col justify-between border-slate-200 hover:border-slate-300">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-base font-bold text-slate-900">Enterprise</h3>
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-enterprise border-slate-300">
                                        <svg class="w-3 h-3 check-enterprise hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mb-4">Kapasitas tanpa batas dengan fitur kecerdasan lanjutan.</p>
                                <div class="mb-4">
                                    <div class="text-2xl font-extrabold text-purple-700 price-enterprise-display">Rp 1.999.000</div>
                                    <span class="text-xs text-slate-400 cycle-enterprise-display">/bulan (ditagih tahunan)</span>
                                </div>
                                <ul class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                                    <li class="flex items-center gap-2">
                                        <span class="text-purple-600 font-bold">&check;</span>
                                        <span><strong>Karyawan Tanpa Batas</strong></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-purple-600 font-bold">&check;</span>
                                        <span>Multi-Cabang & Multi-Divisi</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-purple-600 font-bold">&check;</span>
                                        <span>Unlimited Mesin X Solutions</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-purple-600 font-bold">&check;</span>
                                        <span><strong>RMIH Lanjutan (Asisten AI Cerdas)</strong></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-purple-600 font-bold">&check;</span>
                                        <span>Dukungan Dedicated Account 24/7</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Eksekusi Upgrade -->
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 leading-relaxed text-center sm:text-left">
                            Aktivasi paket dilakukan secara instan. Organisasi Anda dapat langsung melanjutkan seluruh kegiatan presensi dan operasional tanpa gangguan.
                        </div>

                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition shadow-md flex items-center justify-center gap-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Buka Kunci Akun & Aktifkan Paket</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bantuan & Narahubung -->
            <div class="text-center text-xs text-slate-400 space-y-1">
                <p>Membutuhkan invoice resmi perusahaan atau penyesuaian paket khusus korporasi?</p>
                <p>Hubungi Customer Success RMIH di <a href="mailto:billing@rmih.app" class="text-blue-600 hover:underline">billing@rmih.app</a> atau WhatsApp Hotline <strong>+62 811-2345-6789</strong>.</p>
            </div>

        </div>
    </main>

    <script>
        let currentCycle = 'annual';
        let currentPlan = 'operations';

        const prices = {
            starter: { monthly: 'Rp 299.000', annual: 'Rp 249.000' },
            operations: { monthly: 'Rp 899.000', annual: 'Rp 749.000' },
            enterprise: { monthly: 'Rp 2.499.000', annual: 'Rp 1.999.000' }
        };

        function setBillingCycle(cycle) {
            currentCycle = cycle;
            document.getElementById('selected-cycle-input').value = cycle;

            const btnM = document.getElementById('tab-monthly');
            const btnA = document.getElementById('tab-annual');

            if (cycle === 'monthly') {
                btnM.className = 'px-4 py-1.5 font-semibold rounded-lg transition bg-white text-slate-900 shadow-2xs';
                btnA.className = 'px-4 py-1.5 font-semibold rounded-lg transition text-slate-600';
            } else {
                btnA.className = 'px-4 py-1.5 font-semibold rounded-lg transition bg-white text-slate-900 shadow-2xs flex items-center gap-1.5';
                btnM.className = 'px-4 py-1.5 font-semibold rounded-lg transition text-slate-600';
            }

            ['starter', 'operations', 'enterprise'].forEach(plan => {
                document.querySelector(`.price-${plan}-display`).textContent = prices[plan][cycle];
                document.querySelector(`.cycle-${plan}-display`).textContent = cycle === 'annual' ? '/bulan (ditagih tahunan)' : '/bulan (ditagih bulanan)';
            });
        }

        function selectPlan(planSlug) {
            currentPlan = planSlug;
            document.getElementById('selected-plan-input').value = planSlug;

            ['starter', 'operations', 'enterprise'].forEach(p => {
                const card = document.getElementById(`card-${p}`);
                const radio = document.querySelector(`.radio-${p}`);
                const check = document.querySelector(`.check-${p}`);

                if (p === planSlug) {
                    card.className = 'plan-card cursor-pointer p-5 rounded-xl border-2 transition relative flex flex-col justify-between border-blue-600 bg-blue-50/40 ring-2 ring-blue-100';
                    radio.className = `w-5 h-5 rounded-full border flex items-center justify-center radio-${p} border-blue-600 bg-blue-600 text-white`;
                    check.classList.remove('hidden');
                } else {
                    card.className = 'plan-card cursor-pointer p-5 rounded-xl border-2 transition relative flex flex-col justify-between border-slate-200 hover:border-slate-300';
                    radio.className = `w-5 h-5 rounded-full border flex items-center justify-center radio-${p} border-slate-300`;
                    check.classList.add('hidden');
                }
            });
        }

        // Initialize state
        setBillingCycle('annual');
        selectPlan('operations');
    </script>
</body>
</html>
