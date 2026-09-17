@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('projects.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Daftar Proyek
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">{{ $project->name }}</h1>
            @if($project->code)
            <p class="text-xs text-gray-400 font-mono">{{ $project->code }}</p>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                Status: {{ ucfirst($project->status) }}
            </span>
        </div>
    </div>

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Tasks -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Tugas dalam Proyek</h3>
                    <span class="text-xs text-gray-500">{{ $project->tasks->count() }} Tugas Terdaftar</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($project->tasks as $t)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                        <div>
                            <a href="{{ route('tasks.show', $t->id) }}" class="text-sm font-semibold text-gray-900 hover:text-primary-600">
                                {{ $t->title }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">Tenggat: {{ $t->due_at ? $t->due_at->format('d M Y') : '-' }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-semibold text-gray-600">{{ $t->progress_percent }}%</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-gray-400">
                        Belum ada tugas yang ditautkan ke proyek ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Team Members -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px] pb-2 border-b border-gray-100 mb-3">Anggota Tim Proyek</h4>
                <div class="space-y-3">
                    @forelse($project->members as $m)
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs">
                            {{ substr($m->employee?->user?->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-800">{{ $m->employee?->user?->name ?? 'Pegawai' }}</p>
                            <p class="text-[10px] text-gray-400 capitalize">{{ str_replace('_', ' ', $m->role ?? 'Anggota') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400">Belum ada anggota tim.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
