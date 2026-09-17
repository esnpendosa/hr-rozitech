@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('customers.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Daftar Pelanggan
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">{{ __('field_jobs.title') ?? 'Pekerjaan Lapangan (Field Jobs)' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('field_jobs.subtitle') ?? 'Pantau jadwal penugasan teknisi/sales ke lokasi klien dengan verifikasi geolokasi' }}</p>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Tugas Lapangan</th>
                        <th class="py-3.5 px-4 font-semibold">Klien / Lokasi</th>
                        <th class="py-3.5 px-4 font-semibold">Teknisi Bertugas</th>
                        <th class="py-3.5 px-4 font-semibold">Jadwal Visit</th>
                        <th class="py-3.5 px-4 font-semibold">Check-in GPS</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($jobs as $job)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-gray-900">{{ $job->title }}</span>
                            @if($job->description)
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $job->description }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-700">
                            <span class="font-medium text-gray-900">{{ $job->customer?->name ?? '-' }}</span>
                            <span class="block text-gray-400">{{ $job->site_name ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs font-medium text-gray-800">
                            {{ $job->assignee?->user?->name ?? 'Belum Ditugaskan' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600">
                            {{ $job->scheduled_at ? $job->scheduled_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600">
                            {{ $job->checked_in_at ? $job->checked_in_at->format('d M Y H:i') : 'Belum check-in' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $job->status === 'completed' ? 'bg-green-100 text-green-800' : ($job->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            Belum ada penugasan pekerjaan lapangan yang dijadwalkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
