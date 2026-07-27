@extends('layouts.admin')

@section('title', 'Data Kelas')
@section('page_title', 'Data Kelas')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Kelas</h1>
    <p class="text-sm text-gray-500 mt-1">Tambah dan kelola daftar kelas SDN KAWU 1.</p>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 text-sm rounded">
    <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="flat-card p-6 bg-white h-fit">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Kelas Baru</h2>
        <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kelas</label>
                <input type="text" name="nama_kelas" class="w-full px-4 py-2 flat-input"
                    placeholder="Contoh: Kelas 1, Kelas 2-A" required>
                @error('nama_kelas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Wali Kelas</label>
                <select name="wali_kelas" class="w-full px-4 py-2 flat-input bg-white" required>
                    <option value="">-- Pilih Wali Kelas --</option>

                    @foreach($gurus as $guru)
                    <option value="{{ $guru->nama_lengkap }}">{{ $guru->nama_lengkap }}</option>
                    @endforeach
                </select>
                @error('wali_kelas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Simpan Kelas
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 flat-card overflow-hidden bg-white">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-20">No</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Nama Kelas</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($kelas as $index => $k)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $k->nama_kelas }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full font-medium">Aktif</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-gray-400">Belum ada data kelas. Silakan input di
                        form sebelah kiri.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection