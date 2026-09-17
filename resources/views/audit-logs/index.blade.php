@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Audit Trail &amp; Log Keamanan Sistem</h1>
            <p class="text-sm text-gray-500 mt-1">Catatan aktivitas sistem yang tidak dapat diubah (append-only) untuk kepatuhan &amp; transparansi</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Waktu Kejadian</th>
                        <th class="py-3.5 px-4 font-semibold">Aksi</th>
                        <th class="py-3.5 px-4 font-semibold">Entitas Terkait</th>
                        <th class="py-3.5 px-4 font-semibold">ID Entitas</th>
                        <th class="py-3.5 px-4 font-semibold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                            <span class="font-medium text-gray-900">{{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs font-medium text-gray-900">
                            {{ class_basename($log->entity_type) }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs font-mono text-gray-500">
                            {{ Str::limit($log->entity_id, 16) }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs font-mono text-gray-600">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500">
                            Belum ada riwayat audit log keamanan yang tercatat.
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
