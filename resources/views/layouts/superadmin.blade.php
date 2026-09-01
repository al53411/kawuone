<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Superadmin Dashboard')</title>

    <!-- Tailwind CSS & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <style>
    /* 1. Kotak Data & Tabel Solid Flat */
    .flat-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
    }

    /* 2. Kolom Input Form Flat Sempurna */
    .flat-input {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: #1e293b;
        outline: none;
        transition: border-color 0.15s ease-in-out;
    }

    .flat-input:focus {
        background-color: #ffffff;
        border-color: #10b981;
    }

    /* Scrollbar Custom Flat */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 0px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Pattern Ombak CSS untuk Sidebar */
    .bg-wave-pattern {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 1000' opacity='0.08'%3E%3Cpath fill='%2310b981' d='M0 192l48 16c48 16 144 48 240 32s192-80 288-96 192 16 240 32l48 16v800H0z'/%3E%3Cpath fill='%23059669' d='M0 480l48 21.3C96 523 192 565 288 544s192-107 288-107 192 64 240 96l48 32v435H0z'/%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom center;
    }
    </style>
</head>

<body class="bg-slate-100 font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden relative">

                <!-- ================= SIDEBAR ================= -->
        <aside class="w-64 bg-slate-900 text-white min-h-screen flex flex-col justify-between p-4 flex-shrink-0 z-20">
            <div>
                <!-- Logo / Header Sidebar -->
                <div class="px-3 py-4 mb-4 border-b border-slate-800">
                    <h2 class="text-lg font-bold tracking-wider text-white uppercase">Panel Utama</h2>
                    <p class="text-[10px] text-emerald-400 font-semibold tracking-widest uppercase">Superadmin</p>
                </div>

                <!-- Menu Navigasi -->
                <nav class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pusat Kontrol</p>
                    
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold bg-emerald-500 text-white shadow-sm">
                        <i class="fa-solid fa-chart-pie text-sm"></i> Dashboard Pusat
                    </a>

                    <div class="pt-4">
                        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Kelola Sekolah</p>
                        <!-- Tambahkan link menu sekolah Anda di sini -->
                    </div>

                    <div class="pt-4">
                        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Monitoring Lintas Role</p>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-user-shield"></i> Lihat Mode Admin
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-chalkboard-user"></i> Lihat Mode Guru
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Tombol Logout -->
            <div class="pt-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold rounded-xl border border-rose-500/20 transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar / Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" onclick="toggleSidebar()"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-20 hidden md:hidden"></div>

        <!-- ================= MAIN CONTENT AREA ================= -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Navbar Top -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shrink-0">
                <div class="flex items-center space-x-4">
                    <button onclick="toggleSidebar()"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none md:hidden">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span class="font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded text-xs">SUPERADMIN</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                        <span class="text-gray-400">@yield('page_title', 'Dashboard Pusat')</span>
                    </div>
                </div>

                <!-- User Info Ringkas -->
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <span class="text-sm font-semibold text-slate-700 block leading-tight">{{ Auth::user()->name ?? 'Super Admin' }}</span>
                        <span class="text-[11px] text-slate-400">System Administrator</span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-emerald-100">
                        {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8 bg-wave-light">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Javascript Handlers -->
    <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    function toggleDropdown(id, arrowId) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById(arrowId);

        if (dropdown && arrow) {
            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Flash Notification Handlers
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
    });
    @endif
    </script>
</body>

</html>