@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('notifications.title') ?? 'Pusat Pemberitahuan' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('notifications.subtitle') ?? 'Daftar pesan sistem, pembaruan tugas, pengingat target, dan notifikasi kehadiran' }}</p>
        </div>
        <div>
            <button onclick="markAllRead()" class="inline-flex items-center px-3.5 py-2 border border-gray-300 text-xs font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Tandai Semua Dibaca
            </button>
        </div>
    </div>

    <!-- Notification List -->
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden shadow-sm">
        @forelse($notifications as $n)
        <div class="p-4.5 flex items-start justify-between gap-4 hover:bg-gray-50 transition {{ !$n->read_at ? 'bg-primary-50/20' : '' }}">
            <div class="flex items-start space-x-3.5">
                <div class="p-2 rounded-lg {{ !$n->read_at ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $n->title }}</h3>
                        @if(!$n->read_at)
                        <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $n->body }}</p>
                    <span class="text-[11px] text-gray-400 mt-1.5 block">{{ $n->created_at ? $n->created_at->diffForHumans() : '-' }}</span>
                </div>
            </div>

            @if(!$n->read_at)
            <button onclick="markRead('{{ $n->id }}')" class="text-xs font-semibold text-primary-600 hover:text-primary-800 whitespace-nowrap">
                Tandai Dibaca
            </button>
            @endif
        </div>
        @empty
        <div class="py-12 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-sm">Tidak ada notifikasi baru untuk Anda.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>

<script>
function markRead(id) {
    fetch(`/api/v1/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => window.location.reload());
}

function markAllRead() {
    fetch('/api/v1/notifications/read-all', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => window.location.reload());
}
</script>
@endsection
