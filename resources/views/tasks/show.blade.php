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
                    Kembali ke Daftar
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-1">{{ $task->title }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">Dibuat oleh {{ $task->creator?->name ?? 'Sistem' }} &bull; {{ $task->created_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Detail, Checklist, Evidence -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-2">Deskripsi Tugas</h3>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $task->description ?? 'Tidak ada deskripsi detail untuk tugas ini.' }}</p>
            </div>

            <!-- Checklists Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Daftar Langkah Kerja (Checklist)</h3>
                        <p class="text-xs text-gray-500">Tandai butir pekerjaan yang telah Anda selesaikan</p>
                    </div>
                    <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full">
                        {{ $task->progress_percent }}% Selesai
                    </span>
                </div>

                <div class="space-y-2">
                    @forelse($task->checklists as $item)
                    <div class="flex items-center space-x-3 p-2.5 rounded-lg hover:bg-gray-50 border border-gray-100">
                        <input type="checkbox" {{ $item->is_completed ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" disabled>
                        <span class="text-sm {{ $item->is_completed ? 'line-through text-gray-400' : 'text-gray-800 font-medium' }}">{{ $item->title }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-2">Belum ada item checklist kerja.</p>
                    @endforelse
                </div>
            </div>

            <!-- Evidences Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-2">Bukti Pelaksanaan (Evidence)</h3>
                <p class="text-xs text-gray-500 mb-4">Foto, dokumen atau berkas hasil pekerjaan lapangan</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @forelse($task->evidences as $ev)
                    <div class="border border-gray-200 rounded-lg p-3 text-center bg-gray-50">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="block text-xs font-semibold text-gray-700 truncate">{{ ucfirst($ev->type) }}</span>
                        <span class="block text-[10px] text-gray-400">{{ $ev->created_at?->format('d M H:i') }}</span>
                    </div>
                    @empty
                    <p class="col-span-full text-xs text-gray-400 py-3">Belum ada bukti yang diunggah.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Meta, Assignees, History -->
        <div class="space-y-6">
            <!-- Meta Details -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-3 text-xs">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px] pb-2 border-b border-gray-100">Informasi Tugas</h4>
                <div class="flex justify-between">
                    <span class="text-gray-400">Proyek:</span>
                    <span class="font-medium text-gray-800">{{ $task->project?->name ?? 'Mandiri' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Prioritas:</span>
                    <span class="font-semibold text-gray-800">{{ ucfirst($task->priority) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Mulai:</span>
                    <span class="text-gray-800">{{ $task->start_at?->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Tenggat:</span>
                    <span class="font-semibold text-gray-800">{{ $task->due_at?->format('d M Y H:i') ?? '-' }}</span>
                </div>
            </div>

            <!-- Assignees -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px] pb-2 border-b border-gray-100 mb-3">Pelaksana Ditugaskan</h4>
                <div class="space-y-2">
                    @forelse($task->assignees as $assignee)
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs">
                            {{ substr($assignee->employee?->user?->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-800">{{ $assignee->employee?->user?->name ?? 'Pegawai' }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $assignee->employee?->employee_number ?? '-' }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400">Belum ada pelaksana.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
