@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')
@section('header', 'Ringkasan Sistem Sekolah Pusat')

@section('content')

<!-- Flash Message Notifikasi Sukses / Error -->
@if(session('success'))
<div
    class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg flex items-center justify-between shadow-sm">
    <div class="flex items-center space-x-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg flex items-center justify-between shadow-sm">
    <div class="flex items-center space-x-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-xl"></i>
        <span class="text-sm font-medium text-rose-800">{{ session('error') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif

<!-- STATS CARDS -->
<!-- STATS CARDS (Ubah grid-cols-4 menjadi grid-cols-2 md:grid-cols-3 lg:grid-cols-5) -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">

    <!-- Card 1: Total Sekolah -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Sekolah</p>
            <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalSekolah ?? 0 }}</h3>
        </div>
        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
            <i class="fa-solid fa-school text-2xl"></i>
        </div>
    </div>

    <!-- Card NEW: Validasi Jurnal (%) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Validasi Jurnal</p>
            <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $persenValidasi ?? 0 }}%</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $sekolahValidasi ?? 0 }} dari {{ $totalSekolah ?? 0 }}
                Sekolah</p>
        </div>
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
            <i class="fa-solid fa-book-bookmark text-2xl"></i>
        </div>
    </div>

    <!-- Card 2: Kepala Sekolah -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kepala Sekolah</p>
            <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalKepsek ?? 0 }}</h3>
        </div>
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
            <i class="fa-solid fa-user-tie text-2xl"></i>
        </div>
    </div>

    <!-- Card 3: Total Guru -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Guru</p>
            <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalGuru ?? 0 }}</h3>
        </div>
        <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
            <i class="fa-solid fa-chalkboard-user text-2xl"></i>
        </div>
    </div>

    <!-- Card 4: Tenaga Teknis -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tenaga Teknis</p>
            <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalTendik ?? 0 }}</h3>
        </div>
        <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
            <i class="fa-solid fa-screwdriver-wrench text-2xl"></i>
        </div>
    </div>

</div>

<!-- MAIN DATA TABLE: DAFTAR SEKOLAH -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Sekolah & Penanggung Jawab</h2>
            <p class="text-sm text-gray-500">Kelola unit sekolah dan akun Kepala Sekolah terkait</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.kepsek.create') }}"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kepsek / Sekolah
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100">
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nama Sekolah</th>
                    <th class="px-6 py-3">NPSN / Alamat</th>
                    <th class="px-6 py-3">Kepala Sekolah</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @forelse($sekolahs as $index => $sekolah)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium">{{ ($sekolahs->firstItem() ?? 1) + $index }}</td>
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $sekolah->nama_sekolah }}</td>
                    <td class="px-6 py-4">
                        <span class="block font-medium text-gray-800">{{ $sekolah->npsn ?? '-' }}</span>
                        <span
                            class="text-xs text-gray-500">{{ $sekolah->alamat_sekolah ?? $sekolah->alamat ?? 'Alamat Belum Diatur' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        // Mengambil user pertama dengan role 'kepsek'
                        $kepsek = optional($sekolah->users)->firstWhere('role', 'kepsek');
                        @endphp

                        @if($kepsek)
                        <div class="font-semibold text-gray-800">{{ $kepsek->name }}</div>
                        <div class="text-xs text-gray-500">{{ $kepsek->email }}</div>
                        @elseif($sekolah->nama_kepsek)
                        {{-- Fallback jika mengambil dari kolom nama_kepsek langsung di tabel sekolah --}}
                        <div class="font-semibold text-gray-800">{{ $sekolah->nama_kepsek }}</div>
                        <div class="text-xs text-gray-500">NIP: {{ $sekolah->nip_kepsek ?? '-' }}</div>
                        @else
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Belum Ada Kepsek
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('superadmin.sekolah.edit', $sekolah->id) }}"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                title="Edit Data Sekolah">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('superadmin.sekolah.destroy', $sekolah->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sekolah ini? Semua data terkait mungkin akan ikut terhapus.');"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                    title="Hapus Sekolah">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <i class="fa-solid fa-school-circle-xmark text-4xl mb-2"></i>
                        <p>Belum ada data sekolah yang terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($sekolahs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $sekolahs->links() }}
    </div>
    @endif
</div>

@endsection