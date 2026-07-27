@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Siswa')

@section('content')
<!-- Section Header (Dibuat flat tanpa shadow) -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Data Siswa</h1>
        <p class="text-gray-500">Kelola seluruh data siswa aktif {{ $profilSekolah->nama_sekolah }}.</p>
    </div>
    <div>
        <a href="{{ route('admin.siswa.create') }}"
            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded transition space-x-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Siswa</span>
        </a>
    </div>
</div>

<!-- TABLE CARD (Menggunakan .flat-card agar border solid dan tanpa shadow) -->
<div class="flat-card overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-16">No</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">NISN</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Nama Lengkap</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Kelas</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($siswas as $index => $siswa)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm text-gray-500 text-center font-medium">{{ $index + 1 }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $siswa->nisn }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $siswa->nama_siswa }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $siswa->kelas->nama_kelas }}</td>
                <td class="px-6 py-4 text-sm text-center">
                    <div class="flex justify-center space-x-2">
                        <!-- Tombol Edit & Hapus dengan border flat yang tegas -->
                        <a href="{{ route('admin.siswa.edit', $siswa->id) }}"
                            class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center text-amber-600 hover:bg-amber-50 transition"><i
                                class="fa-solid fa-pen text-xs"></i></a>
                        <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST"
                            onsubmit="return confirm('Hapus data siswa ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center text-red-600 hover:bg-red-50 transition"><i
                                    class="fa-solid fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada data siswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection