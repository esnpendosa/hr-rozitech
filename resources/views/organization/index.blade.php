@extends('layouts.app')
@section('title', 'Profil & Struktur Perusahaan')

@section('content')
<div class="max-w-6xl space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Profil &amp; Struktur Perusahaan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola identitas badan usaha, kantor cabang, divisi, jabatan, dan struktur tim kerja.</p>
        </div>
        <div>
            <a href="{{ route('settings.company') }}"
                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Ubah Profil Perusahaan</span>
            </a>
        </div>
    </div>

    <!-- Card Profil Perusahaan -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-gray-100 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white font-black text-xl flex items-center justify-center shadow-xs">
                    {{ strtoupper(substr($company?->name ?? $tenant?->name ?? 'P', 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-900">{{ $company?->name ?? $tenant?->name ?? 'Perusahaan' }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Organisasi Aktif
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Workspace Tenant: <span class="font-mono text-gray-600">{{ $tenant?->slug ?? '-' }}</span></p>
                </div>
            </div>
            <a href="{{ route('settings.company') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 self-start sm:self-auto">
                <span>Edit Kontak &amp; Alamat</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-5 text-xs">
            <div>
                <span class="text-gray-400 block mb-1">Email Perusahaan</span>
                <span class="font-semibold text-gray-800">{{ $company?->email ?? 'Belum diisi' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Nomor Telepon</span>
                <span class="font-semibold text-gray-800">{{ $company?->phone ?? 'Belum diisi' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Situs Web</span>
                @if($company?->website)
                    <a href="{{ $company->website }}" target="_blank" class="font-semibold text-blue-600 hover:underline truncate block">
                        {{ $company->website }}
                    </a>
                @else
                    <span class="text-gray-400">Belum diisi</span>
                @endif
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Kota / Domisili</span>
                <span class="font-semibold text-gray-800">{{ $company?->city ?? 'Belum diatur' }}</span>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 pt-2 border-t border-gray-50">
                <span class="text-gray-400 block mb-1">Alamat Kantor Pusat</span>
                <span class="text-gray-700 leading-relaxed">{{ $company?->address ?? 'Alamat kantor operasional belum dilengkapi. Silakan lengkapi di Pengaturan Perusahaan.' }}</span>
            </div>
        </div>
    </div>

    <!-- Modul Struktur Organisasi (Hub) -->
    <div>
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Modul Struktur &amp; Hierarki Organisasi</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Cabang -->
            <a href="{{ route('branches.index') }}" class="bg-white p-5 rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-xs transition group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center mb-3 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $branchesCount }}</div>
                <h4 class="text-xs font-bold text-gray-800 group-hover:text-blue-600 mt-1 transition">Kantor Cabang</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Kelola lokasi kantor &amp; unit operasional</p>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-blue-600">
                    <span>Buka Cabang</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- 2. Departemen -->
            <a href="{{ route('departments.index') }}" class="bg-white p-5 rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-xs transition group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center mb-3 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $departmentsCount }}</div>
                <h4 class="text-xs font-bold text-gray-800 group-hover:text-blue-600 mt-1 transition">Departemen / Divisi</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Struktur fungsi operasional perusahaan</p>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-blue-600">
                    <span>Buka Departemen</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- 3. Jabatan -->
            <a href="{{ route('positions.index') }}" class="bg-white p-5 rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-xs transition group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center mb-3 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $positionsCount }}</div>
                <h4 class="text-xs font-bold text-gray-800 group-hover:text-blue-600 mt-1 transition">Jabatan &amp; Posisi</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Tingkatan karier dan spesialisasi kerja</p>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-blue-600">
                    <span>Buka Jabatan</span>
                    <span>&rarr;</span>
                </div>
            </a>

            <!-- 4. Tim Kerja -->
            <a href="{{ route('teams.index') }}" class="bg-white p-5 rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-xs transition group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center mb-3 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-black text-gray-900">{{ $teamsCount }}</div>
                <h4 class="text-xs font-bold text-gray-800 group-hover:text-blue-600 mt-1 transition">Tim Kerja</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Kelompok lintas divisi dan squad proyek</p>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-blue-600">
                    <span>Buka Tim</span>
                    <span>&rarr;</span>
                </div>
            </a>

        </div>
    </div>

    <!-- Ringkasan SDM Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Sumber Daya Manusia</span>
            <h3 class="text-base font-bold text-gray-900 mt-0.5">Total {{ $employeesCount }} Karyawan Terdaftar</h3>
            <p class="text-xs text-gray-500 mt-0.5">Daftarkan anggota tim dan hubungkan dengan departemen atau cabang kerja terkait.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-white hover:bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-200 transition shadow-2xs shrink-0 text-center">
            Kelola Data Karyawan &rarr;
        </a>
    </div>
</div>
@endsection
