@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('fingerprint.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Perangkat
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">{{ __('fingerprint.mappings_title') ?? 'Pemetaan PIN Mesin ke Pegawai' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('fingerprint.mappings_subtitle') ?? 'Hubungkan User ID / Enroll ID di mesin fingerprint dengan data pegawai' }}</p>
        </div>
        <div>
            <button onclick="document.getElementById('mapModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Pemetaan
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Pegawai</th>
                        <th class="py-3.5 px-4 font-semibold">NIP / ID</th>
                        <th class="py-3.5 px-4 font-semibold">Mesin Fingerprint</th>
                        <th class="py-3.5 px-4 font-semibold">PIN / ID di Mesin</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($mappings as $mapping)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $mapping->employee?->user?->name ?? 'Tidak Ditemukan' }}</div>
                            <div class="text-xs text-gray-400">{{ $mapping->employee?->user?->email ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-gray-600">
                            {{ $mapping->employee?->employee_number ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-800">{{ $mapping->device?->device_name }}</span>
                            <span class="block text-xs text-gray-400 font-mono">{{ $mapping->device?->device_sn }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-primary-600">
                            {{ $mapping->device_pin }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $mapping->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $mapping->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-500">
                            Belum ada pegawai yang dipetakan ke PIN mesin fingerprint.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $mappings->links() }}
    </div>
</div>
@endsection
