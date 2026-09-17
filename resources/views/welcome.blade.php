<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RMIH - Resource Management Integrated Human Enterprise</title>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" href="/images/rmih-icon.png">
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo & Name -->
            <a href="/" class="flex items-center gap-2.5">
                <img src="/images/rmih-logo.png" alt="RMIH Logo" class="h-9 w-auto">
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#beranda" class="hover:text-blue-600 transition">Beranda</a>
                <a href="#fitur" class="hover:text-blue-600 transition">Fitur</a>
                <a href="#harga" class="hover:text-blue-600 transition">Harga</a>
                <a href="#tentang-kami" class="hover:text-blue-600 transition">Tentang Kami</a>
                <a href="#kontak" class="hover:text-blue-600 transition">Kontak</a>
            </div>

            <!-- Language Switcher & Auth Buttons -->
            <div class="flex items-center gap-3">
                <div class="flex items-center text-xs font-semibold bg-slate-100 p-1 rounded-lg">
                    <a href="/locale/id" class="px-2 py-1 rounded {{ app()->getLocale() === 'id' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">ID</a>
                    <a href="/locale/en" class="px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">EN</a>
                </div>

                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-lg transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        Coba Gratis
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section (Persis Gambar 1) -->
    <section id="beranda" class="relative pt-16 pb-20 md:pt-24 md:pb-28 overflow-hidden bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-medium mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                Satu Platform, Semua Terintegrasi
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight max-w-4xl mx-auto leading-tight">
                Kelola Manusia,<br>Pekerjaan, Target &<br><span class="text-blue-600">Performa dengan Mudah</span>
            </h1>

            <p class="mt-6 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                RMIH adalah aplikasi manajemen sumber daya manusia yang terintegrasi dengan sistem absensi, tugas, target, KPI, serta analitik cerdas untuk meningkatkan produktivitas perusahaan Anda.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition">
                    Mulai Uji Coba Gratis 14 Hari &rarr;
                </a>
                <a href="#harga" class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition">
                    Lihat Pilihan Paket
                </a>
            </div>

            <!-- Floating Product Feature Badges (Mirip Gambar 1) -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    HR & Employee
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9m1.43 11.598a21.92 21.92 0 01-1.5-3.598"/></svg>
                    Attendance Fingerprint
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Task & Target
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    KPI & Performance
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50 text-xs font-bold text-blue-700 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Dashboard AI (V2+)
                </span>
            </div>

            <!-- Trust Badges Under Hero -->
            <div class="mt-12 flex flex-wrap items-center justify-center gap-8 text-xs font-medium text-slate-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Berbasis Web & Mobile
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Keamanan Data Terjamin
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Siap untuk Jutaan Data
                </span>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan Grid (8 Kartu Persis Gambar 1) -->
    <section id="fitur" class="py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-left mb-12">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Fitur Unggulan</h2>
                <p class="text-xs text-slate-500 mt-1">Semua yang Anda butuhkan dalam satu platform terintegrasi</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- 1. Manajemen Karyawan -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Manajemen Karyawan</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Kelola data karyawan, kontrak, dokumen, dan struktur organisasi.</p>
                </div>

                <!-- 2. Absensi & Mesin X Solutions -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9m1.43 11.598a21.92 21.92 0 01-1.5-3.598"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Absensi & Mesin X Solutions</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Integrasi realtime mesin absensi sidik jari & wajah Solution / X-Solutions via ADMS Cloud dan GPS presisi.</p>
                </div>

                <!-- 3. Tugas & Proyek -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Tugas & Proyek</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Buat, pantau dan selesaikan tugas dengan target yang jelas.</p>
                </div>

                <!-- 4. Target & KPI -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Target & KPI</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Ukur kinerja individu dan tim secara real-time dan objektif.</p>
                </div>

                <!-- 5. Payroll & Keuangan -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Payroll & Keuangan</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Proses penggajian, tunjangan, potongan absensi, dan laporan keuangan.</p>
                </div>

                <!-- 6. Customer & CRM -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Customer & CRM</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Kelola pelanggan, layanan lapangan, dan riwayat interaksi tim.</p>
                </div>

                <!-- 7. Laporan & Analitik -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Laporan & Analitik</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Dapatkan laporan lengkap kehadiran, KPI, dan inventaris dalam berbagai format.</p>
                </div>

                <!-- 8. Modul RMIH Lanjutan (V2+) -->
                <div class="bg-white p-6 rounded-xl border-2 border-blue-200 shadow-2xs hover:shadow-sm transition relative">
                    <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-600">Coming Soon</span>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">RMIH Lanjutan (V2+)</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">Analisis data operasional, peramalan beban kerja, rekomendasi prediktif dan konektor ERP tingkat lanjut.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dedicated Section: Integrasi Mesin Absensi X Solutions -->
    <section id="mesin-xsolutions" class="py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    Integrasi Perangkat Keras Biometrik
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Integrasi Mesin Absensi X Solutions</h2>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Hubungkan mesin absensi sidik jari, kartu RFID, dan pendeteksi wajah merk Solution / X-Solutions langsung ke cloud RMIH. Tanpa perlu tarik data manual kabel LAN atau flashdisk.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Kompatibilitas Luas</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Mendukung beragam tipe mesin absensi Solution populer seperti seri X100-C, X302-S, X606, V800, serta mesin face recognition biometrik modern.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Protokol ADMS Realtime Push</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Saat karyawan menempelkan jari atau wajah di mesin X Solutions, data log detik itu juga terdorong otomatis ke server cloud RMIH.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Multi-Cabang & Terpusat</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Kelola puluhan mesin X Solutions di cabang toko, pabrik, gudang logistik, dan kantor pusat secara tersentralisasi dalam satu akun.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-sm transition">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Otomasi Payroll & Lembur</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Data mesin langsung diverifikasi silang dengan shift kerja, kalkulasi jam lembur, potongan keterlambatan, dan penerbitan slip gaji otomatis.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section: 3 Pilihan Paket Berbayar Standar SaaS (Tanpa Paket Gratis) -->
    <section id="harga" class="py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Pilihan Paket Langganan</h2>
                <p class="mt-2 text-sm text-slate-500">Pilih paket terbaik untuk skala operasional organisasi Anda. Semua paket dilengkapi uji coba gratis 14 hari.</p>

                <!-- Billing Cycle Toggle (Bulanan / Tahunan Hemat 17%) -->
                <div class="mt-8 inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200" id="pricing-toggle-container">
                    <button type="button" id="btn-monthly" onclick="switchBilling('monthly')"
                        class="px-5 py-2 text-xs font-bold rounded-lg transition text-slate-600 hover:text-slate-900">
                        Tagihan Bulanan
                    </button>
                    <button type="button" id="btn-annual" onclick="switchBilling('annual')"
                        class="px-5 py-2 text-xs font-bold rounded-lg transition bg-white text-slate-900 shadow-xs flex items-center gap-1.5">
                        <span>Tagihan Tahunan</span>
                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold">Hemat 17%</span>
                    </button>
                </div>
            </div>

            <!-- 3 Pricing Cards Grid (Starter, Operations, Enterprise) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch max-w-6xl mx-auto">
                <!-- 1. Starter Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-7 flex flex-col justify-between shadow-2xs hover:shadow-md transition">
                    <div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 mb-5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Starter</h3>
                        <p class="text-xs text-slate-500 mt-1">Solusi esensial untuk bisnis pemula dan UKM yang ingin merapikan operasional tim</p>

                        <div class="mt-6">
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-bold text-blue-600">Rp</span>
                                <span class="text-3xl font-extrabold text-slate-900 price-starter">249.000</span>
                            </div>
                            <span class="block text-xs text-slate-500 billing-desc-starter">/bulan (tagihan tahunan)</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5 strike-starter"><del>Rp 299.000</del> <strong class="text-blue-600 font-semibold">Hemat 17%</strong></span>
                        </div>

                        <div class="mt-4 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-[11px] font-medium text-slate-600">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Hingga 25 Karyawan
                        </div>

                        <ul class="mt-6 space-y-3 text-xs text-slate-600 border-t border-slate-100 pt-5">
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Absensi Web & Mobile GPS Presisi</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Manajemen Cuti, Izin & Saldo Otomatis</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Berkas Karyawan & Dokumen HR Digital</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Integrasi Mesin Absensi X Solutions Dasar</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Slip Gaji PDF & Laporan Kehadiran Standar</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Aplikasi Mobile Android & iOS</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Bantuan Teknis Cepat via Email</li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100">
                        <a href="{{ route('register') }}?plan=starter&cycle=annual" class="btn-plan-starter w-full block text-center py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-xs">
                            Daftar Paket Starter &rarr;
                        </a>
                        <span class="block text-center text-[10px] text-slate-400 mt-2">14 hari uji coba gratis. Batalkan kapan saja.</span>
                    </div>
                </div>

                <!-- 2. Operations Card (Paling Populer) -->
                <div class="bg-white rounded-2xl border-2 border-blue-600 p-7 flex flex-col justify-between shadow-xl relative ring-4 ring-blue-50">
                    <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-0.5 bg-blue-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1">
                        <span>&#9733;</span> PALING POPULER
                    </span>
                    <div>
                        <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center mb-5 shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Operations</h3>
                        <p class="text-xs text-slate-500 mt-1">Solusi lengkap untuk bisnis berkembang yang mengelola tim lapangan, kantor & multi-shift</p>

                        <div class="mt-6">
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-bold text-blue-600">Rp</span>
                                <span class="text-3xl font-extrabold text-slate-900 price-operations">749.000</span>
                            </div>
                            <span class="block text-xs text-slate-500 billing-desc-operations">/bulan (tagihan tahunan)</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5 strike-operations"><del>Rp 899.000</del> <strong class="text-blue-600 font-semibold">Hemat 17%</strong></span>
                        </div>

                        <div class="mt-4 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-[11px] font-bold text-blue-700">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Hingga 150 Karyawan
                        </div>

                        <ul class="mt-6 space-y-3 text-xs text-slate-600 border-t border-slate-100 pt-5">
                            <li class="flex items-start gap-2 font-semibold text-slate-900"><span class="text-blue-600 font-bold">&check;</span> Semua fitur di paket Starter, ditambah:</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Penggajian Otomatis (Payroll) & Validasi HR</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Alur Persetujuan Bertingkat & Multi-Manajer</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Integrasi Mesin Absensi X Solutions (ADMS Cloud Push)</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Modul Akuntansi & Laporan Keuangan RMIH</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Modul Inventaris & Manajemen Aset Operasional</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Analitik Kinerja SDM & Ekspor Laporan Lanjutan</li>
                            <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">&check;</span> Dukungan Prioritas via Chat/Email 24/7</li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100">
                        <a href="{{ route('register') }}?plan=operations&cycle=annual" class="btn-plan-operations w-full block text-center py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-md">
                            Daftar Paket Operations &rarr;
                        </a>
                        <span class="block text-center text-[10px] text-blue-600 font-semibold mt-2">14 hari uji coba gratis penuh.</span>
                    </div>
                </div>

                <!-- 3. Enterprise Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-7 flex flex-col justify-between shadow-2xs hover:shadow-md transition">
                    <div>
                        <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 mb-5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Enterprise</h3>
                        <p class="text-xs text-slate-500 mt-1">Untuk perusahaan multi-cabang, waralaba, pabrik dan operasional skala besar</p>

                        <div class="mt-6">
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-bold text-purple-600">Rp</span>
                                <span class="text-3xl font-extrabold text-slate-900 price-enterprise">1.999.000</span>
                            </div>
                            <span class="block text-xs text-slate-500 billing-desc-enterprise">/bulan (tagihan tahunan)</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5 strike-enterprise"><del>Rp 2.499.000</del> <strong class="text-purple-600 font-semibold">Hemat 20%</strong></span>
                        </div>

                        <div class="mt-4 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-purple-50 text-[11px] font-bold text-purple-700">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Karyawan Tanpa Batas
                        </div>

                        <ul class="mt-6 space-y-3 text-xs text-slate-600 border-t border-slate-100 pt-5">
                            <li class="flex items-start gap-2 font-semibold text-slate-900"><span class="text-purple-600 font-bold">&check;</span> Semua fitur di paket Operations, ditambah:</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> Multi-Cabang & Multi-Entitas Perusahaan</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> Single Sign-On (SSO SAML / OIDC)</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> Integrasi Multi-Mesin Absensi X Solutions Terpusat</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> SLA Terjamin 99.9%, Pendampingan & Pelatihan</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> Lingkungan Server Khusus / Private Cloud</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> Audit Trail Lengkap & Ekspor Kepatuhan ISO</li>
                            <li class="flex items-start gap-2"><span class="text-purple-600 font-bold">&check;</span> Konektor ERP/Akuntansi Kustom & Fitur RMIH Lanjutan</li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-5 border-t border-slate-100">
                        <a href="{{ route('register') }}?plan=enterprise&cycle=annual" class="btn-plan-enterprise w-full block text-center py-3 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md">
                            Daftar Paket Enterprise &rarr;
                        </a>
                        <span class="block text-center text-[10px] text-purple-600 font-semibold mt-2">Dukungan setup & migrasi data prioritas.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blue Productivity Banner (Persis Bagian Bawah Gambar 1) -->
    <section class="py-16 bg-[#1E3A8A] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Tingkatkan Produktivitas dengan Teknologi Terintegrasi</h2>
                    <p class="mt-4 text-sm text-blue-100 leading-relaxed max-w-xl">
                        RMIH membantu Anda memantau tim, mengelola target, dan mengambil keputusan lebih cepat dengan data yang akurat.
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm rounded-xl transition shadow-sm">
                            <span>Mulai Uji Coba Gratis</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
                <div class="space-y-3 lg:pl-12">
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">&check;</span>
                        <span class="text-sm font-medium">Mudah digunakan</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">&check;</span>
                        <span class="text-sm font-medium">Support penuh 24/7</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">&check;</span>
                        <span class="text-sm font-medium">Harga terjangkau</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">&check;</span>
                        <span class="text-sm font-medium">Keamanan data enterprise</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Client Trust Section -->
    <section class="py-12 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dipercaya oleh Banyak Perusahaan di Seluruh Indonesia</h3>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-8 md:gap-14 text-sm font-bold text-slate-400">
                <span>SACOMTEL</span>
                <span>PT. MAJU JAYA</span>
                <span>GRESIK FIBER</span>
                <span>NUSANTARANET</span>
                <span>PT. BUMI LESTARI</span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-slate-900 text-slate-400 py-12 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <img src="/images/rmih-icon.png" alt="RMIH Icon" class="h-8 w-8 rounded-lg shadow-sm">
                <div>
                    <span class="text-white font-semibold text-sm">RMIH Platform</span>
                    <p class="text-slate-500">Resource Management Integrated Human Enterprise</p>
                </div>
            </div>
            <div class="flex items-center gap-6 text-xs text-slate-400">
                <a href="#" class="hover:text-white transition">Privacy</a>
                <a href="#" class="hover:text-white transition">Terms</a>
                <a href="#" class="hover:text-white transition">Support</a>
                <span>&copy; {{ date('Y') }} RMIH. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <!-- Interactive Toggle Script for Monthly vs Annual Pricing -->
    <script>
        function switchBilling(cycle) {
            const btnMonthly = document.getElementById('btn-monthly');
            const btnAnnual = document.getElementById('btn-annual');

            const priceStarter = document.querySelector('.price-starter');
            const descStarter = document.querySelector('.billing-desc-starter');
            const strikeStarter = document.querySelector('.strike-starter');

            const priceOps = document.querySelector('.price-operations');
            const descOps = document.querySelector('.billing-desc-operations');
            const strikeOps = document.querySelector('.strike-operations');

            const priceEnt = document.querySelector('.price-enterprise');
            const descEnt = document.querySelector('.billing-desc-enterprise');
            const strikeEnt = document.querySelector('.strike-enterprise');

            const btnStarter = document.querySelector('.btn-plan-starter');
            const btnOps = document.querySelector('.btn-plan-operations');
            const btnEnt = document.querySelector('.btn-plan-enterprise');

            const baseUrl = "{{ route('register') }}";

            if (cycle === 'annual') {
                btnAnnual.className = 'px-5 py-2 text-xs font-bold rounded-lg transition bg-white text-slate-900 shadow-xs flex items-center gap-1.5';
                btnMonthly.className = 'px-5 py-2 text-xs font-bold rounded-lg transition text-slate-600 hover:text-slate-900';

                if (priceStarter) priceStarter.textContent = '249.000';
                if (descStarter) descStarter.textContent = '/bulan (tagihan tahunan)';
                if (strikeStarter) strikeStarter.style.display = 'block';

                if (priceOps) priceOps.textContent = '749.000';
                if (descOps) descOps.textContent = '/bulan (tagihan tahunan)';
                if (strikeOps) strikeOps.style.display = 'block';

                if (priceEnt) priceEnt.textContent = '1.999.000';
                if (descEnt) descEnt.textContent = '/bulan (tagihan tahunan)';
                if (strikeEnt) strikeEnt.style.display = 'block';

                if (btnStarter) btnStarter.href = baseUrl + '?plan=starter&cycle=annual';
                if (btnOps) btnOps.href = baseUrl + '?plan=operations&cycle=annual';
                if (btnEnt) btnEnt.href = baseUrl + '?plan=enterprise&cycle=annual';
            } else {
                btnMonthly.className = 'px-5 py-2 text-xs font-bold rounded-lg transition bg-white text-slate-900 shadow-xs';
                btnAnnual.className = 'px-5 py-2 text-xs font-bold rounded-lg transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5';

                if (priceStarter) priceStarter.textContent = '299.000';
                if (descStarter) descStarter.textContent = '/bulan (tagihan bulanan)';
                if (strikeStarter) strikeStarter.style.display = 'none';

                if (priceOps) priceOps.textContent = '899.000';
                if (descOps) descOps.textContent = '/bulan (tagihan bulanan)';
                if (strikeOps) strikeOps.style.display = 'none';

                if (priceEnt) priceEnt.textContent = '2.499.000';
                if (descEnt) descEnt.textContent = '/bulan (tagihan bulanan)';
                if (strikeEnt) strikeEnt.style.display = 'none';

                if (btnStarter) btnStarter.href = baseUrl + '?plan=starter&cycle=monthly';
                if (btnOps) btnOps.href = baseUrl + '?plan=operations&cycle=monthly';
                if (btnEnt) btnEnt.href = baseUrl + '?plan=enterprise&cycle=monthly';
            }
        }
    </script>
</body>
</html>
