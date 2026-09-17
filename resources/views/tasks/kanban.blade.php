@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tampilan Tabel
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">Papan Kanban Tugas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Visualisasi alur pengerjaan dan pembagian beban tugas</p>
        </div>
    </div>

    <!-- Kanban Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 overflow-x-auto pb-4">
        @foreach($columns as $statusKey => $statusLabel)
        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 flex flex-col h-full min-w-[260px]">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-3">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $statusLabel }}</h3>
                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-white text-gray-600 border border-gray-200">
                    {{ count($tasks[$statusKey] ?? []) }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                @forelse($tasks[$statusKey] ?? [] as $t)
                <div class="bg-white p-3.5 rounded-lg border border-gray-200 shadow-sm hover:border-gray-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $t->priority === 'critical' ? 'bg-red-100 text-red-800' : ($t->priority === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-blue-50 text-blue-700') }}">
                            {{ $t->priority }}
                        </span>
                        @if($t->due_at)
                        <span class="text-[11px] text-gray-400">
                            {{ $t->due_at->format('d M') }}
                        </span>
                        @endif
                    </div>

                    <a href="{{ route('tasks.show', $t->id) }}" class="block font-semibold text-sm text-gray-900 mt-2 hover:text-primary-600 line-clamp-2">
                        {{ $t->title }}
                    </a>

                    @if($t->project)
                    <p class="text-xs text-gray-400 mt-1 truncate">{{ $t->project->name }}</p>
                    @endif

                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex items-center -space-x-1">
                            @foreach($t->assignees->take(2) as $assignee)
                            <div class="w-5 h-5 rounded-full bg-primary-100 border border-white flex items-center justify-center text-primary-700 text-[10px] font-bold">
                                {{ substr($assignee->employee?->user?->name ?? 'U', 0, 1) }}
                            </div>
                            @endforeach
                        </div>
                        <span class="text-[11px] text-gray-500 font-medium">{{ $t->progress_percent }}%</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-gray-400">
                    Tidak ada tugas
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
