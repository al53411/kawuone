@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Siswa')

@section('content')
<body class="bg-slate-100 p-8">
    <div class="max-w-2xl bg-white rounded-xl shadow-sm border p-8 mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Data Guru</h1>
        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">NIP</label>
                <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 bg-gray-50 text-sm">
                @error('nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 bg-gray-50 text-sm">
                @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $guru->jabatan) }}" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 bg-gray-50 text-sm">
                    @error('jabatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Golongan</label>
                    <input type="text" name="golongan" value="{{ old('golongan', $guru->golongan) }}" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 bg-gray-50 text-sm">
                    @error('golongan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="pt-6 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.guru.index') }}" class="px-4 py-2.5 border rounded-lg text-sm font-semibold">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection