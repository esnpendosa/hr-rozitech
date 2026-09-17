@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Greeting & Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Selamat Pagi, {{ auth()->user()?->name ?? 'User' }}</h1>
                <span class="text-xs px-2.5 py-0.5 rounded-full {{ $roleName === 'owner' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-blue-50 text-blue-700 border-blue-100' }} font-semibold border">
                    {{ $roleLabel }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Workspace: <strong>{{ $tenant?->name ?? 'Organisasi' }}</strong> &bull; Ringkasan operasional hari ini.
            </p>
        </div>
        <div class="flex items-center gap-3 text-xs text-slate-500 bg-white border border-slate-200 px-3 py-2 rounded-xl shadow-xs">
            <div class="flex items-center gap-1.5 font-medium text-slate-700">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</span>
            </div>
            <span class="text-slate-300">|</span>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <select class="bg-transparent border-none text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer">
                    <option value="">Semua Cabang</option>
                    @forelse($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @empty
                        <option value="main">Kantor Utama (Default)</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    @if($totalEmployees === 0)
        <!-- Onboarding Card untuk Akun / Organisasi Baru -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 shadow-2xs">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-blue-600 text-white rounded-full text-[10px] font-bold uppercase tracking-wider mb-2">
                        Langkah Awal Workspace Baru
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Selamat Datang di RMIH Platform!</h2>
                    <p class="text-xs text-slate-600 mt-1 max-w-2xl leading-relaxed">
                        Organisasi <strong>{{ $tenant?->name }}</strong> berhasil dibuat. Saat ini data workspace masih kosong. Silakan mulai langkah awal untuk mengaktifkan seluruh modul sistem:
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
                <a href="/employees/create" class="bg-white p-4 rounded-xl border border-blue-100 hover:border-blue-400 hover:shadow-xs transition group">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center font-bold text-xs mb-2 transition">
                        1
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-700">Tambah Karyawan</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Daftarkan data staf pertama Anda ke sistem.</p>
                </a>

                <a href="/fingerprint" class="bg-white p-4 rounded-xl border border-blue-100 hover:border-blue-400 hover:shadow-xs transition group">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center font-bold text-xs mb-2 transition">
                        2
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-700">Mesin X Solutions</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Hubungkan mesin biometrik absensi via ADMS.</p>
                </a>

                <a href="/tasks" class="bg-white p-4 rounded-xl border border-blue-100 hover:border-blue-400 hover:shadow-xs transition group">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center font-bold text-xs mb-2 transition">
                        3
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-700">Buat Tugas / Target</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Kelola proyek dan penugasan tim secara rapi.</p>
                </a>

                <a href="/ai-assistant" class="bg-white p-4 rounded-xl border border-blue-100 hover:border-blue-400 hover:shadow-xs transition group">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center font-bold text-xs mb-2 transition">
                        4
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-700">Asisten RMIH</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Konsultasikan aturan ketenagakerjaan &amp; payroll.</p>
                </a>
            </div>
        </div>
    @endif

    <!-- 4 Core Stat Cards (Real Data) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Karyawan -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Karyawan</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ $totalEmployees }}</div>
            <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                @if($totalEmployees > 0)
                    <span class="text-emerald-600">Status aktif di organisasi</span>
                @else
                    <span>Belum ada karyawan</span>
                @endif
            </div>
        </div>

        <!-- 2. Hadir Hari Ini -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Hadir Hari Ini</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ $presentToday }}</div>
            <div class="mt-1 flex items-center gap-1 text-[11px] text-blue-600 font-medium">
                @if($totalEmployees > 0)
                    <span>{{ $attendanceRate }}% dari total karyawan</span>
                @else
                    <span class="text-slate-400">0% rasio absensi</span>
                @endif
            </div>
        </div>

        <!-- 3. Tugas Aktif -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Tugas Aktif</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ $activeTasks }}</div>
            <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                @if($activeTasks > 0)
                    <span class="text-emerald-600">{{ $completedTasks }} tugas selesai</span>
                @else
                    <span>Tidak ada tugas berjalan</span>
                @endif
            </div>
        </div>

        <!-- 4. Target Tercapai -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Target Tercapai</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ $targetProgressPercent }}%</div>
            <div class="mt-1 flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                @if($totalTargets > 0)
                    <span class="text-emerald-600">{{ $completedTargets }} dari {{ $totalTargets }} selesai</span>
                @else
                    <span>Belum ada target dibuat</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Analytics & Alerts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- 1. Progress Target (Donut Chart) - 3 Kolom -->
        <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Progress Target</h2>
            <div class="py-4 flex items-center justify-center relative">
                <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-slate-100" stroke-width="3.5" stroke="currentColor" fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="text-blue-600" stroke-dasharray="{{ $targetProgressPercent }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                </svg>
                <div class="absolute flex flex-col items-center justify-center text-center">
                    <span class="text-2xl font-black text-slate-900">{{ $targetProgressPercent }}%</span>
                    <span class="text-[10px] text-slate-400 font-medium">Ketercapaian</span>
                </div>
            </div>
            <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Target Selesai
                    </span>
                    <span class="font-bold text-slate-800">{{ $completedTargets }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Tugas Berjalan
                    </span>
                    <span class="font-bold text-slate-800">{{ $inProgressTasks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Tugas Antrean
                    </span>
                    <span class="font-bold text-slate-800">{{ $todoTasks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Overdue
                    </span>
                    <span class="font-bold text-slate-800">{{ $overdueTasks }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Integrasi Mesin Absensi X Solutions & Operasional - 5 Kolom -->
        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Status Mesin Absensi X Solutions</h2>
                    <span class="text-[11px] px-2 py-0.5 rounded-full {{ $onlineDevices > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} font-semibold">
                        {{ $onlineDevices }} / {{ $totalDevices }} Unit Online
                    </span>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Protokol Komunikasi:</span>
                        <span class="font-bold text-slate-800">ADMS Cloud Push (Real-Time)</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Jumlah Mesin Terdaftar:</span>
                        <span class="font-bold text-slate-800">{{ $totalDevices }} Perangkat</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Presensi Hari Ini:</span>
                        <span class="font-bold text-slate-800">{{ $presentToday }} Transaksi Masuk</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Karyawan Terlambat:</span>
                        <span class="font-bold text-amber-600">{{ $lateToday }} Orang</span>
                    </div>
                </div>

                @if($totalDevices === 0)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Belum ada mesin absensi X Solutions yang terhubung. Hubungkan mesin biometrik Anda untuk sinkronisasi log otomatis.</span>
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <a href="/fingerprint" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                    <span>Buka Pengaturan Mesin Absensi</span>
                    <span>&rarr;</span>
                </a>
                <a href="/ai-assistant" class="text-slate-500 hover:text-slate-700 font-medium">
                    Konsultasi Asisten &rarr;
                </a>
            </div>
        </div>

        <!-- 3. Notifikasi & Peringatan - 4 Kolom -->
        <div class="lg:col-span-4 bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Notifikasi & Peringatan</h2>
                    <a href="/notifications" class="text-[11px] text-blue-600 hover:text-blue-700 font-medium">Lihat Semua &rarr;</a>
                </div>

                <div class="space-y-2.5">
                    @if($overdueTasks > 0)
                        <div class="p-2.5 rounded-lg border border-rose-100 bg-rose-50/40 flex items-start gap-2.5">
                            <span class="p-1.5 rounded-md bg-rose-100 text-rose-600 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-800">{{ $overdueTasks }} tugas overdue</span>
                                    <span class="text-[10px] text-slate-400">Peringatan</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 truncate">Terdapat pekerjaan yang melewati batas waktu</p>
                            </div>
                        </div>
                    @endif

                    @if($pendingApprovals > 0)
                        <div class="p-2.5 rounded-lg border border-purple-100 bg-purple-50/40 flex items-start gap-2.5">
                            <span class="p-1.5 rounded-md bg-purple-100 text-purple-600 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-800">{{ $pendingApprovals }} permohonan cuti</span>
                                    <span class="text-[10px] text-slate-400">Approval</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 truncate">Menunggu verifikasi dan persetujuan</p>
                            </div>
                        </div>
                    @endif

                    @if($totalEmployees === 0)
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-start gap-2.5">
                            <span class="p-1 rounded-md bg-blue-100 text-blue-600 shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div class="text-xs text-slate-600 leading-relaxed">
                                <strong>Organisasi Anda Siap Digunakan:</strong> Tambahkan data karyawan atau undang tim untuk memulai operasional.
                            </div>
                        </div>
                    @else
                        <div class="p-2.5 rounded-lg border border-slate-100 flex items-start gap-2.5">
                            <span class="p-1.5 rounded-md bg-emerald-50 text-emerald-600 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-semibold text-slate-800 block">Sistem Operasional Normal</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Tidak ada insiden server atau sinkronisasi</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-2 text-center border-t border-slate-100 mt-3">
                <a href="/attendance/leaves" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Kelola Pengajuan Cuti &rarr;</a>
            </div>
        </div>
    </div>

    {{-- AI Assistant Box --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="flex flex-col sm:flex-row items-stretch">
            {{-- Left: dark panel --}}
            <div class="bg-slate-900 sm:w-72 p-6 flex flex-col justify-between shrink-0">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white leading-snug">Asisten RMIH</h3>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">Tanya apa saja seputar data HR, kehadiran, aturan ketenagakerjaan, dan payroll perusahaan Anda.</p>
                </div>
                <a href="/ai-assistant" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 bg-white text-slate-900 text-xs font-bold rounded-lg hover:bg-slate-100 transition w-fit">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Buka Asisten
                </a>
            </div>

            {{-- Right: example prompts --}}
            <div class="flex-1 p-6">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Contoh pertanyaan yang bisa Anda tanyakan</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach([
                        ['q' => 'Berapa total karyawan yang hadir hari ini?', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['q' => 'Apa aturan lembur sesuai PP 35/2021?', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['q' => 'Siapa karyawan yang belum absen hari ini?', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['q' => 'Bagaimana cara menghitung PPh 21 TER?', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ] as $item)
                    <a href="{{ route('ai.index', ['q' => $item['q']]) }}" class="flex items-start gap-2.5 p-3 rounded-lg border border-slate-100 hover:border-blue-200 hover:bg-blue-50/40 transition group cursor-pointer">
                        <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5 transition">
                            <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                        </span>
                        <span class="text-[11px] text-slate-600 group-hover:text-slate-900 leading-relaxed transition">{{ $item['q'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
