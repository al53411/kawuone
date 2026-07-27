@extends('layouts.admin')

@section('title', 'Data Guru')
@section('page_title', 'Guru')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Data Guru</h1>
        <p class="text-gray-500">Kelola seluruh data siswa aktif {{ $profilSekolah->nama_sekolah }}.</p>
    </div>
    <a href="{{ route('admin.guru.create') }}"
        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded transition space-x-2">
        <i class="fa-solid fa-plus text-xs"></i> Tambah Guru
    </a>
</div>

<div class="flat-card overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-16">No</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">NIP</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Nama Lengkap</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Jabatan</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Golongan</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($gurus as $index => $guru)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $guru->nip }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $guru->nama_lengkap }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $guru->jabatan }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    <span
                        class="px-2 py-0.5 bg-gray-100 border border-gray-300 text-gray-800 text-xs font-bold rounded">{{ $guru->golongan }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-center">
                    <div class="flex justify-center space-x-2">
                        <a href="{{ route('admin.guru.edit', $guru->id) }}"
                            class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center text-amber-600 hover:bg-amber-50 transition"><i
                                class="fa-solid fa-pen text-xs"></i></a>
                        <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST"
                            onsubmit="return confirm('Hapus data guru ini?')" class="inline">
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
                <td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada data guru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection