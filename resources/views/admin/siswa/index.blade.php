@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Siswa')

@section('content')
<div class="w-full space-y-4 sm:space-y-6">

    <!-- Section Header (Full width & Responsive Gap) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Data Siswa</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola seluruh data siswa aktif
                {{ Auth::user()?->sekolah?->nama_sekolah ?? $profilSekolah?->nama_sekolah ?? 'Sekolah' }}.
            </p>
        </div>
        <div class="w-full sm:w-auto">
            <a href="{{ route('admin.siswa.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm rounded-lg transition space-x-2 active:bg-blue-800 shadow-sm">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Siswa</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar Section -->
    <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-gray-200 shadow-sm">
        <form method="GET" action="{{ route('admin.siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            
            <!-- Input Search Nama / NISN -->
            <div class="sm:col-span-6 lg:col-span-7 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari nama siswa atau NISN..." 
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>

            <!-- Dropdown Filter Kelas -->
            <div class="sm:col-span-4 lg:col-span-3">
                <select name="kelas_id" class="w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="">-- Semua Kelas --</option>
                    @if(isset($listKelas))
                        @foreach($listKelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs sm:text-sm rounded-lg transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
                @if(request()->has('search') || request()->has('kelas_id'))
                    <a href="{{ route('admin.siswa.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition text-xs" title="Reset Filter">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- MAIN DATA CONTAINER -->
    <div class="flat-card bg-white rounded-xl border border-gray-200 overflow-hidden w-full shadow-sm">
        
        <!-- 1. TAMPILAN MOBILE (< sm) -->
        <div class="block sm:hidden divide-y divide-gray-100">
            @forelse($siswas as $index => $siswa)
            <div class="p-4 hover:bg-gray-50/80 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <!-- Badge Nomor Urut -->
                        <span class="shrink-0 w-7 h-7 flex items-center justify-center rounded-full bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100 mt-0.5">
                            {{ $loop->iteration }}
                        </span>
                        
                        <!-- Detail Informasi Siswa -->
                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900 text-sm leading-snug truncate">
                                {{ $siswa->nama_siswa ?? $siswa->nama_lengkap ?? '-' }}
                            </h2>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                <span>NISN:</span>
                                <span class="font-mono font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">
                                    {{ $siswa->nisn ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Nama Kelas -->
                    <span class="shrink-0 bg-blue-50 text-blue-700 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-blue-100">
                        {{ $siswa->kelas->nama_kelas ?? '-' }}
                    </span>
                </div>

                <!-- Tombol Aksi di Mobile -->
                <div class="flex items-center justify-end gap-2 mt-3 pt-2.5 border-t border-gray-100">
                    <a href="{{ route('admin.siswa.edit', $siswa->id) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 text-xs font-medium hover:bg-amber-100 transition"
                        title="Edit Data">
                        <i class="fa-solid fa-pen text-[10px]"></i>
                        <span>Edit</span>
                    </a>
                    <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST"
                        onsubmit="return confirm('Hapus data siswa ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition"
                            title="Hapus Data">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 text-xs">
                <i class="fa-solid fa-folder-open text-2xl mb-2 block text-gray-300"></i>
                Tidak ditemukan data siswa yang sesuai.
            </div>
            @endforelse
        </div>

        <!-- 2. TAMPILAN DESKTOP/TABLET (>= sm) -->
        <div class="hidden sm:block overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[640px]">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center w-12 sm:w-16">No</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">NISN</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Nama Lengkap</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Kelas</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center w-28 sm:w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($siswas as $index => $siswa)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm text-gray-500 text-center font-medium">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm font-semibold text-gray-700 whitespace-nowrap font-mono">
                            {{ $siswa->nisn }}
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm font-medium text-gray-900">
                            {{ $siswa->nama_siswa ?? $siswa->nama_lengkap ?? '-' }}
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm text-gray-600 whitespace-nowrap">
                            <span class="inline-flex items-center bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-md border border-blue-100">
                                {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm text-center whitespace-nowrap">
                            <div class="flex justify-center space-x-1.5 sm:space-x-2">
                                <a href="{{ route('admin.siswa.edit', $siswa->id) }}"
                                    class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-amber-600 hover:bg-amber-50 transition active:bg-amber-100"
                                    title="Edit Data">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data siswa ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-red-600 hover:bg-red-50 transition active:bg-red-100"
                                        title="Hapus Data">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-xs sm:text-sm">
                            <i class="fa-solid fa-folder-open text-2xl mb-2 block text-gray-300"></i>
                            Tidak ditemukan data siswa yang sesuai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection