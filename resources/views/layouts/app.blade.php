<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RMIH') }} - @yield('title', 'Dashboard')</title>
    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CDN for development -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            DEFAULT: '#2563eb',
                            dark: '#1e3a8a',
                        },
                        accent: '#F59E0B',
                    }
                }
            }
        }
    </script>
    <style>
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.625rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #475569;
            transition: background 0.15s, color 0.15s;
            text-decoration: none;
        }
        .nav-item:hover { background: #eff6ff; color: #1d4ed8; }
        .nav-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .nav-item.active .nav-icon { color: #2563eb; }
        .nav-icon { width: 1rem; height: 1rem; color: #94a3b8; flex-shrink: 0; }
        .nav-item:hover .nav-icon { color: #3b82f6; }
        .nav-group-label {
            padding: 0.5rem 0.625rem 0.2rem;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #cbd5e1;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-900">
    <!-- Navbar -->
    <nav class="bg-[#1E3A8A] text-white px-6 py-2.5 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <img src="/images/rmih-logo.png" alt="RMIH Logo" class="h-8 w-auto bg-white rounded px-2 py-0.5 shadow-xs">
            <span class="text-xs text-blue-200 hidden md:block">Resource Management Integrated Human Enterprise</span>
        </div>
        <div class="flex items-center gap-3">
            <!-- Language Toggle -->
            <div class="flex gap-1 text-sm">
                <a href="/locale/id" class="px-2 py-1 rounded {{ app()->getLocale() === 'id' ? 'bg-white text-blue-900 font-semibold' : 'text-blue-200 hover:text-white' }}">ID</a>
                <span class="text-blue-300">|</span>
                <a href="/locale/en" class="px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-white text-blue-900 font-semibold' : 'text-blue-200 hover:text-white' }}">EN</a>
            </div>
            <!-- Jam Real-Time WIB -->
            <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 bg-blue-900/60 border border-blue-700/50 rounded-lg text-xs font-mono font-medium text-blue-100">
                <svg class="w-3.5 h-3.5 text-blue-300 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="nav-live-clock">{{ now()->format('H:i:s') }} WIB</span>
            </div>
            <!-- Notification Bell -->
            <a href="{{ route('notifications.index') }}" class="relative p-1.5 rounded-lg hover:bg-blue-700 transition" title="Notifikasi">
                <svg class="w-5 h-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </a>
            <!-- User + Logout -->
            <div class="flex items-center gap-2 border-l border-blue-700 pl-3">
                <span class="text-sm text-blue-200 hidden sm:block">{{ auth()->user()?->name ?? '' }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg hover:bg-blue-700 transition" title="Keluar">
                        <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @php
        $currentTenant = auth()->user()?->tenant_id ? \App\Models\Tenant::find(auth()->user()->tenant_id) : null;
    @endphp

    @if($currentTenant && $currentTenant->isTrial())
        <!-- Banner Uji Coba Gratis -->
        <div class="bg-amber-50 border-b border-amber-200 px-6 py-2.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-amber-900">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-amber-600 text-white font-bold rounded text-[10px] uppercase tracking-wider">Uji Coba 14 Hari</span>
                <span>Masa uji coba gratis Anda tersisa <strong>{{ $currentTenant->trialDaysRemaining() }} hari</strong>. Setelah periode ini berakhir, akun akan otomatis terkunci.</span>
            </div>
            <a href="{{ route('subscription.locked') }}" class="font-bold text-blue-700 hover:underline flex items-center gap-1 shrink-0">
                <span>Upgrade Paket Berbayar</span>
                <span>&rarr;</span>
            </a>
        </div>
    @endif

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-56 min-h-screen bg-white border-r border-slate-100 px-3 py-5 hidden md:flex flex-col overflow-y-auto sticky top-0 h-screen">
            <nav class="flex-1 space-y-0.5">

                {{-- Dashboard --}}
                <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>{{ __('common.dashboard') }}</span>
                </a>

                {{-- SDM --}}
                <p class="nav-group-label">SDM</p>
                <a href="/employees" class="nav-item {{ request()->is('employees*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>{{ __('employee.employees') }}</span>
                </a>
                <a href="/organization" class="nav-item {{ request()->is('organization*') || request()->is('branches*') || request()->is('departments*') || request()->is('positions*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ __('organization.company') }}</span>
                </a>

                {{-- Kehadiran --}}
                <p class="nav-group-label">Kehadiran</p>
                <a href="/attendance" class="nav-item {{ request()->is('attendance*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Absensi</span>
                </a>
                <a href="/fingerprint" class="nav-item {{ request()->is('fingerprint*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9m1.43 11.598a21.92 21.92 0 01-1.5-3.598"/></svg>
                    <span>Mesin Absensi</span>
                </a>

                {{-- Pekerjaan --}}
                <p class="nav-group-label">Pekerjaan</p>
                <a href="/tasks" class="nav-item {{ request()->is('tasks*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>{{ __('task.tasks') }}</span>
                </a>
                <a href="/projects" class="nav-item {{ request()->is('projects*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span>Proyek</span>
                </a>
                <a href="/targets" class="nav-item {{ request()->is('targets*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Target &amp; Sasaran</span>
                </a>
                <a href="/kpi" class="nav-item {{ request()->is('kpi*') || request()->is('performance*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>KPI &amp; Performa</span>
                </a>

                {{-- Bisnis --}}
                <p class="nav-group-label">Bisnis</p>
                <a href="/customers" class="nav-item {{ request()->is('customers*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Pelanggan</span>
                </a>
                <a href="/accounting" class="nav-item {{ request()->is('accounting*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Payroll &amp; Keuangan</span>
                </a>
                <a href="/inventory" class="nav-item {{ request()->is('inventory*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Inventaris &amp; Aset</span>
                </a>

                {{-- Sistem --}}
                <p class="nav-group-label">Sistem</p>
                <a href="/reports" class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan</span>
                </a>
                <a href="/notifications" class="nav-item {{ request()->is('notifications*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>Notifikasi</span>
                </a>
                <a href="/audit-logs" class="nav-item {{ request()->is('audit-logs*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Audit Log</span>
                </a>
                <a href="/settings/company" class="nav-item {{ request()->is('settings*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan</span>
                </a>

            </nav>

            {{-- Asisten AI — pinned bottom --}}
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="/ai-assistant" class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium {{ request()->is('ai-assistant*') ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }} transition group">
                    <div class="w-6 h-6 rounded-md {{ request()->is('ai-assistant*') ? 'bg-blue-600' : 'bg-slate-200 group-hover:bg-slate-300' }} flex items-center justify-center transition flex-shrink-0">
                        <svg class="w-3.5 h-3.5 {{ request()->is('ai-assistant*') ? 'text-white' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="font-semibold text-[11px]">Asisten RMIH</div>
                        <div class="text-[10px] text-slate-400">Tanya data &amp; aturan HR</div>
                    </div>
                </a>
            </div>

        </aside>




        <!-- Main content -->
        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded border border-green-200 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded border border-red-200 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    <script>
        function tickNavClock() {
            const el = document.getElementById('nav-live-clock');
            if (el) {
                const d = new Date();
                const hh = String(d.getHours()).padStart(2, '0');
                const mm = String(d.getMinutes()).padStart(2, '0');
                const ss = String(d.getSeconds()).padStart(2, '0');
                el.textContent = `${hh}:${mm}:${ss} WIB`;
            }
        }
        tickNavClock();
        setInterval(tickNavClock, 1000);
    </script>
</body>
</html>
