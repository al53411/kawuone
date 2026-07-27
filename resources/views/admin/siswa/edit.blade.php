@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Siswa')

@section('content')
    <div class="flex h-screen overflow-hidden">
        <div class="w-64 bg-slate-900 text-slate-300 flex flex-col shadow-xl z-20">
            <div class="h-16 flex items-center justify-center bg-slate-950 px-6 border-b border-slate-800">
                <span class="text-lg font-bold tracking-wider text-white">SDN KAWU 1</span>
            </div>
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                <a href="{{ route('admin.siswa.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-semibold">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span class="text-sm">Data Siswa</span>
                </a>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b flex items-center px-8 shadow-sm">
                <span class="text-sm text-gray-500">Admin &gt; Siswa &gt; Edit</span>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Edit Data Siswa</h1>
                </div>

                <div class="max-w-2xl bg-white rounded-xl shadow-sm border p-8">
                    <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">NISN</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 bg-gray-50 text-sm">
                                @error('nisn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="nama_siswa" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 bg-gray-50 text-sm">
                                @error('nama_siswa') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                                    <select name="kelas_id" class="w-full px-4 py-2.5 rounded-lg border bg-gray-50 text-sm">
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="w-full px-4 py-2.5 rounded-lg border bg-gray-50 text-sm">
                                        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                                <textarea name="alamat" rows="4" class="w-full px-4 py-2.5 rounded-lg border bg-gray-50 text-sm">{{ old('alamat', $siswa->alamat) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2.5 border rounded-lg text-sm font-semibold">Batal</a>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
@endsection