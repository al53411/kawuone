<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SDN Kawu 1') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        
        <!-- ================= SIDEBAR ================= -->
        <aside class="w-64 bg-slate-900 text-white min-h-screen flex flex-col justify-between shrink-0 shadow-xl">
            <div>
                <!-- Logo & Brand -->
                <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-lg tracking-wide border-b border-slate-800">
                    <span class="text-blue-400 mr-2">🏫</span> SDN Kawu 1
                </div>

                <!-- Navigation Links -->
                <nav class="mt-6 px-4 space-y-1">
                    @if(Auth::user()->role === 'guru')
                        <!-- Menu Guru -->
                        <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Menu Guru
                        </div>

                        <a href="{{ route('guru.dashboard') }}" 
                           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('guru.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3">📊</span> Dashboard
                        </a>

                        <a href="{{ route('guru.siswa.index') }}" 
                           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('guru.siswa.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3">👨‍🎓</span> Data Siswa
                        </a>

                        <a href="{{ route('guru.jurnal.index') }}" 
                           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('guru.jurnal.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3">📖</span> Jurnal Mengajar
                        </a>
                    @else
                        <!-- Menu Admin -->
                        <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Menu Admin
                        </div>

                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3">📊</span> Dashboard Admin
                        </a>

                        <a href="{{ route('admin.siswa.index') }}" 
                           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.siswa.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3">👨‍🎓</span> Kelola Siswa
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Profile & Logout Bottom Section -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white truncate max-w-[130px]">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-400 rounded-lg hover:bg-slate-800 transition">
                            🚪
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT ================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Header -->
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-4 px-6">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>