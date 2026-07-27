@extends('layouts.superadmin')

@section('title', 'Tambah Akun Kepala Sekolah')
@section('page_title', 'Tambah Akun Kepala Sekolah')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Akun Kepala Sekolah</h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan akun kepala sekolah baru beserta sekolah yang dipimpinnya.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('superadmin.dashboard') }}" 
               class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition duration-150">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi / Error Validation -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg">
            <div class="flex items-center">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl mr-3"></i>
                <h3 class="text-sm font-semibold text-rose-800">Terdapat beberapa kesalahan input:</h3>
            </div>
            <ul class="mt-2 ml-8 list-disc text-xs text-rose-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Input Flat Card -->
    <div class="flat-card p-6 md:p-8 bg-white border border-slate-200 rounded-xl shadow-sm">
        <form action="{{ route('superadmin.kepsek.store') }}" method="POST">
            @csrf

            <!-- SECTION 1: Informasi Akun -->
            <div class="mb-6">
                <h2 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-user-gear text-emerald-600"></i>
                    <span>Informasi Akun Login</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Drs. Ahmad Dahlan, M.Pd"
                            class="w-full px-3.5 py-2.5 flat-input focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Alamat Email (Username) <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            placeholder="kepsek@sekolah.sch.id"
                            class="w-full px-3.5 py-2.5 flat-input focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3.5 py-2.5 flat-input focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            placeholder="Ulangi password di atas"
                            class="w-full px-3.5 py-2.5 flat-input focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Penugasan Sekolah -->
            <div class="mb-8">
                <h2 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-school text-emerald-600"></i>
                    <span>Penugasan Unit Sekolah</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIP / NIK -->
                    <div>
                        <label for="nip" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            NIP / NIK (Opsional)
                        </label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                            placeholder="19800101 200501 1 001"
                            class="w-full px-3.5 py-2.5 flat-input focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <!-- Pilih Sekolah / Asal Sekolah -->
                    <div>
                        <label for="sekolah_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Pilih Unit Sekolah <span class="text-rose-500">*</span>
                        </label>
                        <select name="sekolah_id" id="sekolah_id" required
                            class="w-full px-3.5 py-2.5 flat-input focus:ring-2 focus:ring-emerald-500/20 bg-white">
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
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="reset" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition duration-150">
                    Reset Form
                </button>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition duration-150 flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Akun Kepsek</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection