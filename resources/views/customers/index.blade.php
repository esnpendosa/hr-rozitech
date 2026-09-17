@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('customers.title') ?? 'Klien & Pelanggan Perusahaan' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('customers.subtitle') ?? 'Direktori data pelanggan korporat, kontak penanggung jawab, dan lokasi kantor' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('customers.jobs') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Semua Pekerjaan Lapangan
            </a>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Pelanggan Baru
            </button>
        </div>
    </div>

    <!-- Customers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($customers as $c)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-gray-300 transition flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $c->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($c->status) }}
                        </span>
                        <h3 class="text-base font-semibold text-gray-900 mt-2 hover:text-primary-600">
                            <a href="{{ route('customers.show', $c->id) }}">{{ $c->name }}</a>
                        </h3>
                        @if($c->company_name)
                        <p class="text-xs text-gray-500">{{ $c->company_name }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-xs text-gray-500">
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span>Email:</span>
                        <span class="font-medium text-gray-800">{{ $c->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span>Telepon:</span>
                        <span class="text-gray-800 font-mono">{{ $c->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span>Pekerjaan Lapangan:</span>
                        <span class="font-medium text-primary-600">{{ $c->field_jobs_count }} Job Terjadwal</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-400">{{ $c->city ?? 'Indonesia' }}</span>
                <a href="{{ route('customers.show', $c->id) }}" class="font-medium text-primary-600 hover:text-primary-800">Detail & Job &rarr;</a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-xl border border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pelanggan terdaftar</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai tambahkan database klien untuk mengaitkan penugasan lapangan teknisi.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection
