<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Mandiri & Pilih Paket - RMIH Platform</title>
    <link rel="icon" type="image/png" href="{{ asset('images/rmih-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { DEFAULT: '#2563EB', dark: '#1E3A8A' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Logo & Brand Header -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/rmih-logo.png') }}" alt="RMIH Platform Logo" class="h-10 w-auto object-contain"
                     onerror="this.onerror=null; this.src='{{ asset('images/rmih-icon.png') }}';">
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                Daftar Mandiri & Aktivasi Paket RMIH
            </h1>
            <p class="text-sm text-slate-500 max-w-xl mx-auto mt-1">
                Pilih paket terbaik untuk kebutuhan perusahaan Anda. Uji coba gratis 14 hari penuh tanpa komitmen di awal.
            </p>
        </div>

        @if($errors->any())
            <div class="max-w-4xl mx-auto mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" id="register-form" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf
            <input type="hidden" name="plan_slug" id="selected-plan-input" value="{{ old('plan_slug', $selectedPlanSlug) }}">
            <input type="hidden" name="billing_cycle" id="selected-cycle-input" value="{{ old('billing_cycle', $selectedCycle) }}">
            <input type="hidden" name="subscription_mode" id="selected-mode-input" value="{{ old('subscription_mode', $selectedPlanSlug === 'trial' ? 'trial' : 'paid') }}">

            <!-- KOLOM KIRI: Data Profil & Perusahaan -->
            <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-6">
                    <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                    <h2 class="text-base font-bold text-slate-900">Informasi Akun & Perusahaan</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Penanggung Jawab <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                            class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Perusahaan / Organisasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required placeholder="Contoh: PT Surya Integrasi Nusantara"
                            class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan sebagai identitas workspace dan cabang organisasi Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Email Bisnis <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@perusahaan.com"
                                class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                No. WhatsApp / Telepon
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890"
                                class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Kata Sandi <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Ulangi Kata Sandi <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required placeholder="Konfirmasi sandi"
                                class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" required checked class="mt-1 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                            <span class="text-xs text-slate-500 leading-relaxed">
                                Saya menyetujui <a href="#" class="text-blue-600 hover:underline">Ketentuan Layanan</a> serta <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a> RMIH Platform.
                            </span>
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                            <span>Selesaikan Pendaftaran & Aktifkan Paket</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                    <p class="text-center text-xs text-slate-500 pt-2">
                        Sudah memiliki akun terdaftar? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk di sini</a>
                    </p>
                </div>
            </div>

            <!-- KOLOM KANAN: Pilihan Paket Langganan & Ringkasan -->
            <div class="lg:col-span-6 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                            <h2 class="text-base font-bold text-slate-900">Pilih Paket Langganan</h2>
                        </div>

                        <!-- Switch Bulanan / Tahunan -->
                        <div class="inline-flex items-center p-0.5 bg-slate-100 rounded-lg border border-slate-200 text-xs">
                            <button type="button" onclick="setBillingCycle('monthly')" id="tab-monthly"
                                class="px-3 py-1 font-semibold rounded-md transition text-slate-600">
                                Bulanan
                            </button>
                            <button type="button" onclick="setBillingCycle('annual')" id="tab-annual"
                                class="px-3 py-1 font-semibold rounded-md transition bg-white text-slate-900 shadow-2xs flex items-center gap-1">
                                <span>Tahunan</span>
                                <span class="text-[9px] px-1 py-0.2 bg-emerald-100 text-emerald-700 font-bold rounded">Hemat 17%</span>
                            </button>
                        </div>
                    </div>

                    <!-- Pilihan Paket Langganan & Coba Gratis -->
                    <div class="space-y-3">
                        <!-- Card Coba Gratis (14 Hari Trial) -->
                        <div onclick="selectTrial()" id="card-trial"
                            class="plan-card cursor-pointer p-4 rounded-xl border-2 transition relative {{ old('subscription_mode', $selectedPlanSlug) === 'trial' ? 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-100' : 'border-slate-200 hover:border-slate-300' }}">
                            <span class="absolute -top-2.5 right-4 px-2 py-0.5 bg-amber-500 text-white text-[9px] font-bold rounded-full uppercase tracking-wider shadow-2xs">
                                Uji Coba Gratis
                            </span>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-trial {{ old('subscription_mode', $selectedPlanSlug) === 'trial' ? 'border-amber-600 bg-amber-600 text-white' : 'border-slate-300' }}">
                                        <svg class="w-3 h-3 check-trial {{ old('subscription_mode', $selectedPlanSlug) === 'trial' ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-900">Coba Gratis 14 Hari</h3>
                                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-1.5 py-0.2 rounded">Akses Penuh</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500">Uji coba semua modul enterprise &bull; Tanpa kartu kredit</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-amber-600">Rp 0</div>
                                    <span class="text-[10px] text-slate-400">14 hari pertama</span>
                                </div>
                            </div>
                            <!-- Peringatan Kunci Akun Otomatis -->
                            <div class="mt-2.5 pt-2 border-t border-amber-200/60 flex items-start gap-1.5 text-[11px] text-amber-800 leading-snug">
                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span><strong>Catatan:</strong> Setelah masa uji coba 14 hari habis, akun Anda akan <strong>terkunci otomatis</strong> dan wajib melakukan upgrade paket untuk terus beroperasi.</span>
                            </div>
                        </div>

                        <!-- Starter Card -->
                        <div onclick="selectPlan('starter', 299000, 249000, 'Starter', 'Hingga 25 Karyawan')" id="card-starter"
                            class="plan-card cursor-pointer p-4 rounded-xl border-2 transition relative {{ $selectedPlanSlug === 'starter' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-starter {{ $selectedPlanSlug === 'starter' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300' }}">
                                        <svg class="w-3 h-3 check-starter {{ $selectedPlanSlug === 'starter' ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Starter</h3>
                                        <p class="text-[11px] text-slate-500">Hingga 25 Karyawan &bull; 1 Cabang / Unit</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-slate-900 price-starter-display">Rp 249.000</div>
                                    <span class="text-[10px] text-slate-400 cycle-starter-display">/bln (tahunan)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Operations Card (Paling Populer) -->
                        <div onclick="selectPlan('operations', 899000, 749000, 'Operations', 'Hingga 150 Karyawan')" id="card-operations"
                            class="plan-card cursor-pointer p-4 rounded-xl border-2 transition relative {{ $selectedPlanSlug === 'operations' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300' }}">
                            <span class="absolute -top-2.5 right-4 px-2 py-0.5 bg-blue-600 text-white text-[9px] font-bold rounded-full uppercase tracking-wider shadow-2xs">
                                Paling Populer
                            </span>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-operations {{ $selectedPlanSlug === 'operations' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300' }}">
                                        <svg class="w-3 h-3 check-operations {{ $selectedPlanSlug === 'operations' ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Operations</h3>
                                        <p class="text-[11px] text-slate-500">Hingga 150 Karyawan &bull; Multi-Shift & Mesin X Solutions</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-blue-600 price-operations-display">Rp 749.000</div>
                                    <span class="text-[10px] text-slate-400 cycle-operations-display">/bln (tahunan)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Enterprise Card -->
                        <div onclick="selectPlan('enterprise', 2499000, 1999000, 'Enterprise', 'Karyawan Tanpa Batas')" id="card-enterprise"
                            class="plan-card cursor-pointer p-4 rounded-xl border-2 transition relative {{ $selectedPlanSlug === 'enterprise' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center radio-enterprise {{ $selectedPlanSlug === 'enterprise' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300' }}">
                                        <svg class="w-3 h-3 check-enterprise {{ $selectedPlanSlug === 'enterprise' ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Enterprise</h3>
                                        <p class="text-[11px] text-slate-500">Karyawan Tanpa Batas &bull; Multi-Cabang & RMIH Lanjutan</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-purple-700 price-enterprise-display">Rp 1.999.000</div>
                                    <span class="text-[10px] text-slate-400 cycle-enterprise-display">/bln (tahunan)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Highlight Fitur Unggulan -->
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Keuntungan Termasuk di Semua Paket:</h4>
                        <ul class="space-y-2 text-xs text-slate-600">
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">&check;</span>
                                <span><strong>Integrasi Mesin Absensi X Solutions</strong> (ADMS Cloud Push otomatis)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">&check;</span>
                                <span>Presensi GPS Mobile Anti-Fake GPS & Geofence Akurat</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">&check;</span>
                                <span>Modul Akuntansi, Payroll Otomatis & Manajemen Inventaris</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">&check;</span>
                                <span>Uji Coba Penuh 14 Hari Tanpa Perlu Kartu Kredit di Awal</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Jaminan & Keamanan -->
                <div class="bg-blue-50/70 rounded-xl border border-blue-100 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <div class="text-xs text-blue-900 leading-relaxed">
                        <strong>Privasi Data Terjamin:</strong> Data karyawan dan mesin absensi Anda dienkripsi secara end-to-end sesuai standar keamanan korporat perbankan dan industri.
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let currentCycle = '{{ $selectedCycle }}';
        let currentPlan = '{{ $selectedPlanSlug }}';
        let currentMode = document.getElementById('selected-mode-input').value || 'paid';

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
                btnM.className = 'px-3 py-1 font-semibold rounded-md transition bg-white text-slate-900 shadow-2xs';
                btnA.className = 'px-3 py-1 font-semibold rounded-md transition text-slate-600';
            } else {
                btnA.className = 'px-3 py-1 font-semibold rounded-md transition bg-white text-slate-900 shadow-2xs flex items-center gap-1';
                btnM.className = 'px-3 py-1 font-semibold rounded-md transition text-slate-600';
            }

            // Update display prices
            ['starter', 'operations', 'enterprise'].forEach(plan => {
                document.querySelector(`.price-${plan}-display`).textContent = prices[plan][cycle];
                document.querySelector(`.cycle-${plan}-display`).textContent = cycle === 'annual' ? '/bln (tahunan)' : '/bln (bulanan)';
            });
        }

        function selectTrial() {
            currentMode = 'trial';
            document.getElementById('selected-mode-input').value = 'trial';
            document.getElementById('selected-plan-input').value = 'operations'; // Base tier for trial

            // Activate trial card
            const trialCard = document.getElementById('card-trial');
            const radioTrial = document.querySelector('.radio-trial');
            const checkTrial = document.querySelector('.check-trial');
            trialCard.className = 'plan-card cursor-pointer p-4 rounded-xl border-2 transition relative border-amber-500 bg-amber-50/50 ring-2 ring-amber-100';
            radioTrial.className = 'w-5 h-5 rounded-full border flex items-center justify-center radio-trial border-amber-600 bg-amber-600 text-white';
            checkTrial.classList.remove('hidden');

            // Deactivate paid cards
            ['starter', 'operations', 'enterprise'].forEach(p => {
                const card = document.getElementById(`card-${p}`);
                const radio = document.querySelector(`.radio-${p}`);
                const check = document.querySelector(`.check-${p}`);
                card.className = 'plan-card cursor-pointer p-4 rounded-xl border-2 transition relative border-slate-200 hover:border-slate-300';
                radio.className = `w-5 h-5 rounded-full border flex items-center justify-center radio-${p} border-slate-300`;
                check.classList.add('hidden');
            });
        }

        function selectPlan(planSlug, monthlyPrice, annualPrice, planName, capacity) {
            currentMode = 'paid';
            currentPlan = planSlug;
            document.getElementById('selected-mode-input').value = 'paid';
            document.getElementById('selected-plan-input').value = planSlug;

            // Deactivate trial card
            const trialCard = document.getElementById('card-trial');
            const radioTrial = document.querySelector('.radio-trial');
            const checkTrial = document.querySelector('.check-trial');
            trialCard.className = 'plan-card cursor-pointer p-4 rounded-xl border-2 transition relative border-slate-200 hover:border-slate-300';
            radioTrial.className = 'w-5 h-5 rounded-full border flex items-center justify-center radio-trial border-slate-300';
            checkTrial.classList.add('hidden');

            ['starter', 'operations', 'enterprise'].forEach(p => {
                const card = document.getElementById(`card-${p}`);
                const radio = document.querySelector(`.radio-${p}`);
                const check = document.querySelector(`.check-${p}`);

                if (p === planSlug) {
                    card.className = 'plan-card cursor-pointer p-4 rounded-xl border-2 transition relative border-blue-600 bg-blue-50/40 ring-2 ring-blue-100';
                    radio.className = `w-5 h-5 rounded-full border flex items-center justify-center radio-${p} border-blue-600 bg-blue-600 text-white`;
                    check.classList.remove('hidden');
                } else {
                    card.className = 'plan-card cursor-pointer p-4 rounded-xl border-2 transition relative border-slate-200 hover:border-slate-300';
                    radio.className = `w-5 h-5 rounded-full border flex items-center justify-center radio-${p} border-slate-300`;
                    check.classList.add('hidden');
                }
            });
        }

        // Initialize state
        setBillingCycle(currentCycle);
        if (currentMode === 'trial') {
            selectTrial();
        }
    </script>
</body>
</html>
