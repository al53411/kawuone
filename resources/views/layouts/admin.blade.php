<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>
/* Kotak Data & Tabel Solid Flat */
.flat-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
}

/* Tombol Dropdown Sidebar Flat */
.flat-dropdown {
    background-color: #0f172a;
    border-left: 3px solid #3b82f6;
}

/* Kolom Input Form Flat Sempurna */
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
    border-color: #3b82f6;
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
</style>

<body class="bg-slate-100 font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden relative">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 w-64 bg-slate-950 text-slate-300 flex flex-col border-r border-slate-800 z-30 transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 ease-in-out h-full">

            <!-- Header Sidebar (PERBAIKAN DI BARIS INI) -->
            <div class="h-16 flex items-center justify-between bg-slate-900 px-6 border-b border-slate-800 shrink-0">
                <div class="flex items-center space-x-2 truncate">
                    <i class="fa-solid fa-graduation-cap text-2xl text-blue-500 shrink-0"></i>
                    <span class="text-white font-bold text-base truncate">
                        {{ Auth::user()->sekolah->nama_sekolah ?? $profilSekolah->nama_sekolah ?? 'Sistem Sekolah' }}
                    </span>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Navigasi Menu -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu Utama</p>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span class="text-sm">Dashboard</span>
                </a>

                <!-- Dropdown Master Data -->
                @php $isAkademikActive = request()->routeIs('admin.sekolah.*', 'admin.siswa.*', 'admin.guru.*',
                'admin.kelas.*'); @endphp
                <div class="space-y-1">
                    <button onclick="toggleDropdown('dropdown-akademik', 'arrow-akademik')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition group focus:outline-none {{ $isAkademikActive ? 'text-white font-medium' : '' }}">
                        <div class="flex items-center space-x-3">
                            <i
                                class="fa-solid fa-server w-5 text-center {{ $isAkademikActive ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }} transition"></i>
                            <span class="font-medium text-sm">Master Data</span>
                        </div>
                        <i id="arrow-akademik"
                            class="fa-solid fa-chevron-down text-xs text-slate-500 group-hover:text-white transition-transform duration-200 {{ $isAkademikActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="dropdown-akademik"
                        class="{{ $isAkademikActive ? '' : 'hidden' }} pl-11 pr-2 py-1 space-y-1 bg-slate-900/40 rounded-lg">
                        <a href="{{ route('admin.sekolah.index') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('admin.sekolah.*') ? 'text-blue-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-school text-xs mr-2"></i> Profil Sekolah
                        </a>
                        <a href="{{ route('admin.siswa.index') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('admin.siswa.*') ? 'text-blue-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-user-graduate text-xs mr-2"></i> Data Siswa
                        </a>
                        <a href="{{ route('admin.guru.index') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('admin.guru.*') ? 'text-blue-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-chalkboard-teacher text-xs mr-2"></i> Data Guru
                        </a>
                        <a href="{{ route('admin.kelas.index') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('admin.kelas.*') ? 'text-blue-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-layer-group text-xs mr-2"></i> Data Kelas
                        </a>
                    </div>
                </div>

                <!-- Dropdown Administrasi -->
                @php $isAdminActive = request()->routeIs('admin.absensi.*', 'admin.kepala-sekolah.jurnal.*'); @endphp
                <div class="space-y-1">
                    <button onclick="toggleDropdown('dropdown-administrasi', 'arrow-administrasi')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition group focus:outline-none {{ $isAdminActive ? 'text-white font-medium' : '' }}">
                        <div class="flex items-center space-x-3">
                            <i
                                class="fa-solid fa-folder-open w-5 text-center {{ $isAdminActive ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }} transition"></i>
                            <span class="font-medium text-sm">Administrasi</span>
                        </div>
                        <i id="arrow-administrasi"
                            class="fa-solid fa-chevron-down text-xs text-slate-500 group-hover:text-white transition-transform duration-200 {{ $isAdminActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="dropdown-administrasi"
                        class="{{ $isAdminActive ? '' : 'hidden' }} pl-11 pr-2 py-1 space-y-1 bg-slate-900/40 rounded-lg">
                        <a href="{{ route('admin.absensi.index') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('admin.absensi.*') ? 'text-blue-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-clipboard-user text-xs mr-2"></i> Absen
                        </a>
                        <a href="{{ route('admin.kepala-sekolah.jurnal.index') }}"
                            class="block py-2 px-3 text-sm rounded-md transition {{ request()->routeIs('admin.kepala-sekolah.jurnal.*') ? 'text-blue-400 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white' }}">
                            <i class="fa-solid fa-book text-xs mr-2"></i> Jurnal Harian
                        </a>
                    </div>
                </div>

                <!-- Dropdown Surat -->
                @php $isSuratActive = request()->routeIs('admin.surat-masuk.*', 'admin.surat-keluar.*'); @endphp
                <div class="space-y-1">
                    <button onclick="toggleDropdown('dropdown-surat', 'arrow-surat')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition group focus:outline-none {{ $isSuratActive ? 'text-white font-medium' : '' }}">
                        <div class="flex items-center space-x-3">
                            <i
                                class="fa-solid fa-envelope w-5 text-center {{ $isSuratActive ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }} transition"></i>
                            <span class="font-medium text-sm">Surat</span>
                        </div>
                        <i id="arrow-surat"
                            class="fa-solid fa-chevron-down text-xs text-slate-500 group-hover:text-white transition-transform duration-200 {{ $isSuratActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="dropdown-surat"
                        class="{{ $isSuratActive ? '' : 'hidden' }} pl-11 pr-2 py-1 space-y-1 bg-slate-900/40 rounded-lg">
                        <a href="#"
                            class="block py-2 px-3 text-sm rounded-md text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-envelope-open-text text-xs mr-2"></i> Surat Masuk
                        </a>
                        <a href="#"
                            class="block py-2 px-3 text-sm rounded-md text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-paper-plane text-xs mr-2"></i> Surat Keluar
                        </a>
                    </div>
                </div>

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

        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" onclick="toggleSidebar()"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-20 hidden md:hidden"></div>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- Navbar Top -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shrink-0">
                <div class="flex items-center space-x-4">
                    <button onclick="toggleSidebar()"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none md:hidden">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span class="font-medium text-gray-700">Admin</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                        <span class="text-gray-400">@yield('page_title', 'Dashboard')</span>
                    </div>
                </div>

                <!-- User Info Ringkas -->
                <div class="flex items-center space-x-3">
                    <span
                        class="text-sm font-semibold text-slate-700 hidden sm:inline">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    <div
                        class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
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