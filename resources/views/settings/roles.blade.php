@extends('layouts.app')
@section('title', 'Peran & Hak Akses (RBAC)')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Peran & Hak Akses (RBAC)</h1>
        <p class="text-sm text-slate-500 mt-1">Struktur peran platform dan perizinan sistem berbasis matriks keamanan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($roles as $role)
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ $role->name }}</span>
                    <span class="text-xs text-slate-400">{{ $role->permissions->count() }} izin</span>
                </div>
                <div class="mt-4 flex flex-wrap gap-1.5 max-h-48 overflow-y-auto">
                    @foreach($role->permissions as $perm)
                        <span class="px-2 py-0.5 text-[11px] font-mono bg-slate-100 text-slate-600 rounded">
                            {{ $perm->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
