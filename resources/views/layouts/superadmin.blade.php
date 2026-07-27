<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Superadmin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

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

/* Scrollbar Flat */
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
</style>

<body class="bg-slate-100 font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden relative">

        <!-- SIDEBAR -->
        <div id="sidebar"
            class="fixed inset-y-0 left-0 w-64 bg-slate-950 text-slate-300 flex flex-col border-r border-slate-800 z-30 transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 ease-in-out">

            <!-- Header Sidebar Superadmin -->
            <div class="h-16 flex items-center justify-between bg-slate-900 px-6 border-b border-slate-800 shrink-0">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-user-shield text-2xl text-emerald-500"></i>
                    <div>
                        <span class="text-white font-bold text-base block leading-tight">PANEL UTAMA</span>
                        <span class="text-[10px] tracking-wider text-emerald-400 uppercase font-semibold">Superadmin</span>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Navigasi Menu -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pusat Kontrol</p>

                <!-- Dashboard Superadmin -->
                <a href="{{ route('superadmin.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-emerald-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span class="text-sm">Dashboard Pusat</span>
                </a>

                <!-- Dropdown Manajemen Sekolah & Kepsek -->
                <div class="space-y-1">
                    <button onclick="toggleDropdown('dropdown-sekolah', 'arrow-sekolah')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition group focus:outline-none">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-school-flag text-slate-400 group-hover:text-emerald-500 transition"></i>
                            <span class="font-medium text-sm">Kelola Sekolah</span>
                        </div>
                        <i id="arrow-sekolah"
                            class="fa-solid fa-chevron-down text-xs text-slate-500 group-hover:text-white transition-transform duration-200 {{ request()->routeIs('superadmin.kepsek.*') ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="dropdown-sekolah" class="{{ request()->routeIs('superadmin.kepsek.*') ? '' : 'hidden' }} pl-11 pr-2 py-1 space-y-1 bg-slate-900/40 rounded-lg">
                        <a href="{{ route('superadmin.kepsek.create') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('superadmin.kepsek.create') ? 'text-emerald-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-user-plus text-xs mr-2"></i> Tambah Kepsek / Akun
                        </a>
                    </div>
                </div>

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Monitoring Lintas Role</p>

                <!-- Akses Cepat Mode Admin Sekolah -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition hover:bg-slate-800 hover:text-white text-slate-400">
                    <i class="fa-solid fa-building-columns text-slate-400"></i>
                    <span class="text-sm">Lihat Mode Admin</span>
                </a>

                <!-- Akses Cepat Mode Guru -->
                <a href="{{ route('guru.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition hover:bg-slate-800 hover:text-white text-slate-400">
                    <i class="fa-solid fa-chalkboard-user text-slate-400"></i>
                    <span class="text-sm">Lihat Mode Guru</span>
                </a>

            </div>

            <!-- Footer Sidebar (Tombol Logout) -->
            <div class="p-4 border-t border-slate-800 bg-slate-900/50 shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-lg text-sm font-medium text-rose-400 bg-rose-500/10 hover:bg-rose-600 hover:text-white transition duration-150 ease-in-out">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar / Logout</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" onclick="toggleSidebar()"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-20 hidden md:hidden"></div>

        <!-- MAIN CONTENT AREA -->
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
                    <div
                        class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-emerald-100">
                        {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                @yield('content')
            </main>
        </div>

    </div>

    <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    // 1. Fungsi Buka / Tutup Sidebar
    function toggleSidebar() {
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    // 2. Fungsi Buka / Tutup Dropdown Menu
    function toggleDropdown(id, arrowId) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById(arrowId);

        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            dropdown.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }

    // 3. Resizing handler
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });
    </script>
</body>

</html>