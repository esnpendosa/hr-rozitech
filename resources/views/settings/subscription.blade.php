@extends('layouts.app')
@section('title', 'Langganan & Kuota')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Paket Langganan & Kuota</h1>
        <p class="text-sm text-slate-500 mt-1">Pilih paket yang sesuai dengan skala operasional organisasi Anda.</p>
    </div>

    <!-- Pricing Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($plans as $plan)
            <div class="bg-white rounded-xl border {{ $plan->slug === 'business' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-slate-200' }} p-5 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $plan->name }}</span>
                        @if($plan->slug === 'business')
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700 rounded">POPULER</span>
                        @endif
                    </div>
                    <div class="mt-4">
                        <span class="text-2xl font-bold text-slate-900">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500">/ bulan</span>
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-4 space-y-2.5">
                        @foreach($plan->features as $feature)
                            <div class="flex items-start gap-2 text-xs text-slate-600">
                                @if($feature->value === 'true')
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}</span>
                                @elseif($feature->value === 'false')
                                    <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span class="text-slate-400 line-through">{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}</span>
                                @else
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="font-medium text-slate-800">{{ $feature->value }} {{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button class="w-full py-2 text-xs font-semibold rounded-lg transition {{ $plan->slug === 'business' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Pilih Paket
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
