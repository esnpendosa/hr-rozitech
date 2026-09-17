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
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">{{ __('fingerprint.sync_history_title') ?? 'Riwayat Sinkronisasi Log Biometrik' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('fingerprint.sync_history_subtitle') ?? 'Audit trail proses tarikan data presensi mesin ke database cloud' }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Waktu Eksekusi</th>
                        <th class="py-3.5 px-4 font-semibold">Perangkat</th>
                        <th class="py-3.5 px-4 font-semibold">Tipe Tarikan</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Total Ditemukan</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Berhasil Dibuat</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Dilewati</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Gagal</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900">{{ $log->started_at?->format('d M Y H:i:s') ?? '-' }}</span>
                            <span class="block text-xs text-gray-400">Durasi: {{ $log->completed_at ? $log->started_at->diffInSeconds($log->completed_at) . 's' : 'Running' }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-semibold text-gray-800">{{ $log->device?->device_name ?? 'Semua Mesin' }}</span>
                            <span class="block text-xs text-gray-400 font-mono">{{ $log->device?->device_sn ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->sync_type === 'manual' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ ucfirst($log->sync_type ?? 'scheduled') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center font-medium">{{ $log->records_fetched }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center font-semibold text-green-600">+{{ $log->records_created }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-gray-500">{{ $log->records_skipped }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-center font-medium {{ $log->records_failed > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $log->records_failed }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $log->status === 'completed' ? 'bg-green-100 text-green-800' : ($log->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-gray-500">
                            Belum ada aktivitas sinkronisasi mesin biometrik yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
