<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RMIH') }} - Masuk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: { DEFAULT: '#2563EB', dark: '#1E3A8A' } }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block mb-3">
                <img src="{{ asset('images/rmih-logo.png') }}" alt="RMIH Platform Logo" class="h-10 w-auto mx-auto object-contain"
                     onerror="this.onerror=null; this.src='{{ asset('images/rmih-icon.png') }}';">
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Akun</h1>
            <p class="text-sm text-slate-500 mt-1">Resource Management Integrated Human Enterprise</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm">
            @if(session('status'))
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-xs">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lupa sandi?</a>
                    </div>
                    <input type="password" name="password" required
                        class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-xs text-slate-600">Ingat saya pada perangkat ini</label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition shadow-sm">
                        Masuk ke Akun
                    </button>
                </div>

                <div class="pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        Belum memiliki akun perusahaan?
                        <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline ml-1">Daftar Mandiri & Pilih Paket</a>
                    </p>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} PT Integrasi Solusi Unggul. All rights reserved.
        </div>
    </div>
</body>
</html>
