@extends('layouts.app')
@section('title', 'Tim Kerja')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('organization') }}" class="hover:text-blue-600 transition">Organisasi</a>
                <span>&rsaquo;</span>
                <span class="text-gray-800 font-semibold">Tim Kerja</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tim Kerja &amp; Project Squad</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola regu kerja lintas divisi, kelompok proyek, dan penanggung jawab tim.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="openTeamModal()"
                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Tim Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs">
        <form method="GET" action="{{ route('teams.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama tim atau kode..."
                    class="w-full pl-10 pr-4 py-2 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div class="flex items-center gap-2">
                <select name="department_id" onchange="this.form.submit()" class="text-xs py-2 px-3 border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ ($departmentId ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl transition">
                    Cari
                </button>
                @if(!empty($search) || !empty($departmentId))
                <a href="{{ route('teams.index') }}" class="px-3 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Tim -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-4">Nama Tim</th>
                        <th class="py-3 px-4">Kode Tim</th>
                        <th class="py-3 px-4">Departemen Induk</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($teams as $t)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="py-3.5 px-4 font-semibold text-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-gray-900 font-bold">{{ $t->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-600">
                            {{ $t->code ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-600">
                            {{ $t->department?->name ?? 'Lintas Departemen' }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-500 max-w-xs truncate">
                            {{ $t->description ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($t->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600">
                                Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <form method="POST" action="{{ route('teams.destroy', $t->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tim ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs p-1">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <div class="max-w-xs mx-auto space-y-2">
                                <svg class="w-10 h-10 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-xs text-gray-500 font-medium">Belum ada data tim kerja terdaftar.</p>
                                <button type="button" onclick="openTeamModal()" class="text-xs text-blue-600 font-bold hover:underline">
                                    + Tambah Tim Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($teams->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $teams->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Tim -->
<div id="modal-team" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4 hidden">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Tambah Tim Kerja Baru</h3>
            <button type="button" onclick="closeTeamModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('teams.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Tim *</label>
                <input type="text" name="name" required placeholder="Contoh: Squad Mobile App / Tim Penagihan"
                    class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kode Tim</label>
                    <input type="text" name="code" placeholder="Contoh: TIM-MOB"
                        class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Departemen Induk</label>
                    <select name="department_id" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                        <option value="">Lintas Departemen</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi / Sasaran Tim</label>
                <textarea name="description" rows="2" placeholder="Fokus pekerjaan atau proyek yang dijalankan..."
                    class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
            </div>
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeTeamModal()" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Simpan Tim Kerja
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modalTeam = document.getElementById('modal-team');
    function openTeamModal() {
        modalTeam.classList.remove('hidden');
    }
    function closeTeamModal() {
        modalTeam.classList.add('hidden');
    }
</script>
@endsection
