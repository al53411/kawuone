@extends('layouts.admin')

@section('title', 'Profil Sekolah')
@section('page_title', 'Profil Sekolah')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Identitas & Profil Sekolah</h1>
        <p class="text-gray-500">Kelola seluruh data siswa aktif {{ $profilSekolah->nama_sekolah }}.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 text-sm rounded">
            <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
        </div>
    @endif

    <div class="flat-card p-6 max-w-2xl bg-white">
        <form action="{{ route('admin.sekolah.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Sekolah</label>
                <input type="text" name="nama_sekolah" 
                       value="{{ old('nama_sekolah', $sekolah->nama_sekolah ?? '') }}" 
                       class="w-full px-4 py-2 flat-input" 
                       placeholder="Contoh: SDN KAWU 1" required>
                @error('nama_sekolah')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">NPSN</label>
                <input type="text" name="npsn" 
                       value="{{ old('npsn', $sekolah->npsn ?? '') }}" 
                       class="w-full px-4 py-2 flat-input" 
                       placeholder="Masukkan NPSN Sekolah">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kepala Sekolah</label>
                <input type="text" name="nama_kepsek" 
                       value="{{ old('nama_kepsek', $sekolah->nama_kepsek ?? '') }}" 
                       class="w-full px-4 py-2 flat-input" 
                       placeholder="Nama beserta gelar">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Sekolah</label>
                <textarea name="alamat" rows="3" 
                          class="w-full px-4 py-2 flat-input" 
                          placeholder="Alamat lengkap sekolah...">{{ old('alamat', $sekolah->alamat ?? '') }}</textarea>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Data Sekolah
                </button>
            </div>
        </form>
    </div>
@endsection