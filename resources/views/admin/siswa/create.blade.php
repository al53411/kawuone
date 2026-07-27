@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Siswa')

@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Siswa Baru</h1>
    <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah ini dengan data yang valid.</p>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <form action="{{ route('admin.siswa.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <div>
                <label for="nisn" class="block text-sm font-semibold text-gray-700 mb-2">NISN</label>
                <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}"
                    class="w-full px-4 py-2.5 rounded-lg border @error('nisn') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror focus:outline-none focus:ring-2 bg-gray-50 text-gray-900 text-sm transition"
                    placeholder="Masukkan NISN siswa">
                @error('nisn')
                <p class="text-xs text-red-500 mt-1 font-medium"><i
                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama_siswa" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                    Lengkap</label>
                <input type="text" name="nama_siswa" id="nama_siswa" value="{{ old('nama_siswa') }}"
                    class="w-full px-4 py-2.5 rounded-lg border @error('nama_siswa') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror focus:outline-none focus:ring-2 bg-gray-50 text-gray-900 text-sm transition"
                    placeholder="Masukkan nama lengkap siswa">
                @error('nama_siswa')
                <p class="text-xs text-red-500 mt-1 font-medium"><i
                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kelas_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <select name="kelas_id" id="kelas_id"
                        class="w-full px-4 py-2.5 rounded-lg border @error('kelas_id') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror focus:outline-none focus:ring-2 bg-gray-50 text-gray-900 text-sm transition">
                        <option value="" disabled selected>Pilih Kelas</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                    <p class="text-xs text-red-500 mt-1 font-medium"><i
                            class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2">Jenis
                        Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin"
                        class="w-full px-4 py-2.5 rounded-lg border @error('jenis_kelamin') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror focus:outline-none focus:ring-2 bg-gray-50 text-gray-900 text-sm transition">
                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                            (L)</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                            (P)</option>
                    </select>
                    @error('jenis_kelamin')
                    <p class="text-xs text-red-500 mt-1 font-medium"><i
                            class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">Alamat
                    Lengkap</label>
                <textarea name="alamat" id="alamat" rows="4"
                    class="w-full px-4 py-2.5 rounded-lg border @error('alamat') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-blue-500 @enderror focus:outline-none focus:ring-2 bg-gray-50 text-gray-900 text-sm transition"
                    placeholder="Masukkan alamat domisili siswa">{{ old('alamat') }}</textarea>
                @error('alamat')
                <p class="text-xs text-red-500 mt-1 font-medium"><i
                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.siswa.index') }}"
                class="px-4 py-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold transition">
                Batal
            </a>
            <button type="submit"
                class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm shadow-blue-600/10 transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Data
            </button>
        </div>
    </form>
</div>
</div>
@endsection