@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Siswa')

@section('content')
<div class="w-full">
    <!-- Section Header (Full width & Responsive Gap) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
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

    <!-- TABLE CARD (Full width di Desktop & Overflow Scroll untuk Mobile) -->
    <div class="flat-card bg-white rounded-xl border border-gray-200 overflow-hidden w-full shadow-sm">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[640px]">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th
                            class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center w-12 sm:w-16">
                            No</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">NISN</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Nama Lengkap</th>
                        <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Kelas</th>
                        <th
                            class="px-4 sm:px-6 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center w-28 sm:w-36">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($siswas as $index => $siswa)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm text-gray-500 text-center font-medium">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm font-semibold text-gray-700 whitespace-nowrap">
                            {{ $siswa->nisn }}
                        </td>
                        <!-- DIBETULKAN: Menggunakan nama_lengkap (Bukan nama_siswa lagi) -->
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm font-medium text-gray-900">
                            {{ $siswa->nama_lengkap ?? '-' }}
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm text-gray-600 whitespace-nowrap">
                            {{ $siswa->kelas->nama_kelas ?? '-' }}
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
                            Belum ada data siswa.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection