@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Tugas &amp; Pekerjaan</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau alur eksekusi pekerjaan, tenggat waktu, dan progres tim</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tasks.kanban') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                Papan Kanban
            </a>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                Proyek
            </a>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Tugas Baru
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Tugas</th>
                        <th class="py-3.5 px-4 font-semibold">Proyek</th>
                        <th class="py-3.5 px-4 font-semibold">Pelaksana</th>
                        <th class="py-3.5 px-4 font-semibold">Prioritas</th>
                        <th class="py-3.5 px-4 font-semibold">Tenggat Waktu</th>
                        <th class="py-3.5 px-4 font-semibold">Progres</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4">
                            <a href="{{ route('tasks.show', $task->id) }}" class="font-semibold text-gray-900 hover:text-primary-600">
                                {{ $task->title }}
                            </a>
                            @if($task->description)
                            <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ Str::limit($task->description, 60) }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600">
                            {{ $task->project?->name ?? 'Non-proyek' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center -space-x-1">
                                @forelse($task->assignees->take(3) as $assignee)
                                <div class="w-6 h-6 rounded-full bg-primary-100 border border-white flex items-center justify-center text-primary-700 text-xs font-bold" title="{{ $assignee->employee?->user?->name }}">
                                    {{ substr($assignee->employee?->user?->name ?? 'U', 0, 1) }}
                                </div>
                                @empty
                                <span class="text-xs text-gray-400">Belum ada</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @php
                                $priorityBadge = match($task->priority) {
                                    'critical' => 'bg-red-100 text-red-800',
                                    'high'     => 'bg-orange-100 text-orange-800',
                                    'medium'   => 'bg-blue-100 text-blue-800',
                                    default    => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $priorityBadge }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-600">
                            {{ $task->due_at ? $task->due_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="w-24 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $task->progress_percent }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 mt-1 block">{{ $task->progress_percent }}%</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('tasks.show', $task->id) }}" class="text-primary-600 hover:text-primary-900 font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-500">
                            Belum ada tugas atau pekerjaan yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
