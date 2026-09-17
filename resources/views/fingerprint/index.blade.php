@extends('layouts.app')
@section('title', 'Mesin Fingerprint & Biometrik')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Mesin Fingerprint &amp; Biometrik</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola perangkat absensi biometrik fisik di seluruh cabang.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('fingerprint.mappings') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-xs font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition shadow-2xs">
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Pemetaan PIN Pegawai
            </a>
            <a href="{{ route('fingerprint.logs') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-xs font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition shadow-2xs">
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Log Sinkronisasi
            </a>
            <button type="button" onclick="openDeviceModal()" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Perangkat
            </button>
        </div>
    </div>

    <!-- Devices Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($devices as $device)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs p-5 hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $device->status === 'online' || $device->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $device->status === 'online' || $device->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ ucfirst($device->status ?? 'active') }}
                        </span>
                        <h3 class="text-base font-bold text-gray-900 mt-2">{{ $device->name ?? $device->device_name ?? 'Mesin Absensi' }}</h3>
                        <p class="text-xs text-gray-400">SN: {{ $device->serial_number ?? $device->device_sn ?? '-' }}</p>
                    </div>
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-xs text-gray-600">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-400">Cabang</span>
                        <span class="font-medium text-gray-800">{{ $device->branch?->name ?? 'Semua Cabang' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-400">IP / Port</span>
                        <span class="font-mono text-gray-800">{{ $device->ip_address ?? 'Cloud' }}:{{ $device->port ?? '4370' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-400">Sinkron Terakhir</span>
                        <span class="text-gray-800">{{ $device->last_sync_at ? $device->last_sync_at->diffForHumans() : 'Belum pernah' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-400">User Terdaftar</span>
                        <span class="font-medium text-gray-800">{{ $device->device_users_count ?? 0 }} Pegawai</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                <button type="button" onclick="testDevice('{{ $device->id }}')" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-200 text-xs font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Tes Koneksi
                </button>
                <button type="button" onclick="syncDevice('{{ $device->id }}')" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Tarik Log
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-200 shadow-2xs">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11m0 0c0 2.473.345 4.866.99 7.132" />
            </svg>
            <h3 class="mt-2 text-sm font-bold text-gray-900">Belum ada mesin biometrik terdaftar</h3>
            <p class="mt-1 text-xs text-gray-500">Hubungkan mesin fingerprint (ZKTeco, Solution, XSolutions, dll) ke cabang perusahaan.</p>
            <div class="mt-4">
                <button type="button" onclick="openDeviceModal()" class="text-xs text-blue-600 font-bold hover:underline">
                    + Tambah Perangkat Pertama
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($devices->hasPages())
    <div class="mt-4">
        {{ $devices->links() }}
    </div>
    @endif
</div>

<!-- Modal Tambah Perangkat Fingerprint -->
<div id="addDeviceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4 hidden">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Tambah Mesin Fingerprint Baru</h3>
            <button type="button" onclick="closeDeviceModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('fingerprint.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Perangkat *</label>
                <input type="text" name="name" required placeholder="Contoh: Mesin Fingerprint Lobby Utama"
                    class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Seri (Serial Number) *</label>
                    <input type="text" name="serial_number" required placeholder="Contoh: SN-ZKTECO-8821"
                        class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kantor Cabang *</label>
                    <select name="branch_id" required class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                        <option value="">Pilih Cabang</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">IP Address (Lokal / Publik)</label>
                    <input type="text" name="ip_address" placeholder="192.168.1.201"
                        class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Port</label>
                    <input type="number" name="port" value="4370"
                        class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Provider / Protokol</label>
                <select name="provider" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                    <option value="xsolutions">XSolutions / ZKTeco Standalone (Port 4370)</option>
                    <option value="solution">Solution Cloud</option>
                    <option value="fingerspot">Fingerspot IO</option>
                </select>
            </div>
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDeviceModal()" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Simpan Perangkat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const addDeviceModal = document.getElementById('addDeviceModal');

function openDeviceModal() {
    if (addDeviceModal) {
        addDeviceModal.classList.remove('hidden');
    }
}

function closeDeviceModal() {
    if (addDeviceModal) {
        addDeviceModal.classList.add('hidden');
    }
}

function testDevice(id) {
    fetch(`/api/v1/fingerprint/devices/${id}/test`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Status diperbarui.');
        window.location.reload();
    })
    .catch(() => alert('Gagal menghubungkan ke perangkat. Periksa koneksi jaringan.'));
}

function syncDevice(id) {
    fetch(`/api/v1/fingerprint/devices/${id}/sync`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ async: false })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Sinkronisasi berhasil dijalankan!');
        window.location.reload();
    })
    .catch(() => alert('Sinkronisasi gagal.'));
}
</script>
@endsection
