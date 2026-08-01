@extends('layouts.superadmin')

@section('title', 'Tambah Akun Kepala Sekolah')
@section('page_title', 'Tambah Akun Kepala Sekolah')

@section('content')
<!-- Pembungkus w-full agar mengisi 100% area konten (konsisten) -->
<div class="w-full">

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="mb-5 p-4 rounded bg-rose-50 border border-rose-200 text-rose-800 flex items-start justify-between">
            <div class="flex items-start space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-semibold">Terdapat beberapa kesalahan input:</h3>
                    <ul class="mt-1 list-disc list-inside text-xs space-y-1 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Card Form Utama Flat Full Width -->
    <div class="flat-card w-full bg-white border border-slate-200 rounded-md overflow-hidden">
        
        <!-- Header Card Flat -->
        <div class="bg-slate-900 px-6 py-3.5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center space-x-3 text-white">
                <i class="fa-solid fa-user-gear text-emerald-400 text-base"></i>
                <h2 class="font-bold text-sm tracking-wide">Tambah Akun Kepala Sekolah Baru</h2>
            </div>
            <a href="{{ route('superadmin.dashboard') }}" 
               class="inline-flex items-center space-x-1.5 px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded border border-slate-700 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('superadmin.kepsek.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- SECTION 1: Informasi Akun Login -->
                <div>
                    <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-user-lock"></i> Informasi Akun Login
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                placeholder="Contoh: Drs. Ahmad Dahlan, M.Pd"
                                class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('name') border-rose-500 @enderror">
                            @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Alamat Email (Username) <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                placeholder="kepsek@sekolah.sch.id"
                                class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('email') border-rose-500 @enderror">
                            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                placeholder="Minimal 8 karakter"
                                class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('password') border-rose-500 @enderror">
                            @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Konfirmasi Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                placeholder="Ulangi password di atas"
                                class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Penugasan Unit Sekolah -->
                <div>
                    <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-school"></i> Penugasan Unit Sekolah
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIP / NIK -->
                        <div>
                            <label for="nip" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                NIP / NIK (Opsional)
                            </label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                                placeholder="19800101 200501 1 001"
                                class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm">
                        </div>

                        <!-- Pilih Unit Sekolah -->
                        <div>
                            <label for="sekolah_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Pilih Unit Sekolah <span class="text-rose-500">*</span>
                            </label>
                            <select name="sekolah_id" id="sekolah_id" required
                                class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('sekolah_id') border-rose-500 @enderror">
                                <option value="" disabled selected>-- Pilih Sekolah --</option>
                                @if(isset($sekolahs) && $sekolahs->count() > 0)
                                    @foreach($sekolahs as $sekolah)
                                        <option value="{{ $sekolah->id }}" {{ old('sekolah_id') == $sekolah->id ? 'selected' : '' }}>
                                            {{ $sekolah->nama_sekolah }} (NPSN: {{ $sekolah->npsn ?? '-' }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Belum ada data sekolah tersimpan</option>
                                @endif
                            </select>
                            @error('sekolah_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                    <button type="reset" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded transition">
                        Reset Form
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Akun Kepsek
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection