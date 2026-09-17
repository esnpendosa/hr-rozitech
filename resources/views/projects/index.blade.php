@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Proyek Perusahaan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola inisiatif bisnis, anggaran, dan pemantauan deliverable tim</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Proyek Baru
            </button>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                        <h3 class="text-base font-semibold text-gray-900 mt-2 hover:text-primary-600">
                            <a href="{{ route('projects.show', $project->id) }}">{{ $project->name }}</a>
                        </h3>
                        @if($project->code)
                        <p class="text-xs text-gray-400 font-mono">{{ $project->code }}</p>
                        @endif
                    </div>
                </div>

                @if($project->description)
                <p class="text-xs text-gray-600 mt-3 line-clamp-2">{{ $project->description }}</p>
                @endif

                <div class="mt-4 space-y-2 text-xs text-gray-500">
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span>Manajer Proyek:</span>
                        <span class="font-medium text-gray-800">{{ $project->manager?->name ?? 'Belum ditentukan' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span>Tenggat Waktu:</span>
                        <span class="text-gray-800">{{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span>Total Pekerjaan:</span>
                        <span class="font-medium text-gray-800">{{ $project->tasks_count }} Tugas &bull; {{ $project->members_count }} Anggota</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progres Keseluruhan</span>
                    <span class="font-bold text-gray-900">{{ $project->progress_percent }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $project->progress_percent }}%"></div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-xl border border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada proyek terdaftar</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai buat proyek untuk mengelompokkan deliverable dan tugas tim.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</div>
@endsection
