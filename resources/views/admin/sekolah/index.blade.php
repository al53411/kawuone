@extends('layouts.admin')

@section('title', 'Profil Sekolah')
@section('page_title', 'Profil Sekolah')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Identitas & Profil Sekolah</h1>
            <p class="text-sm text-gray-500">Kelola data identitas dan kontak resmi unit sekolah.</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
            <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full"></span> Status: Aktif
        </span>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 text-sm rounded-lg flex items-center">
            <i class="fa-solid fa-circle-check mr-2 text-green-600"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 text-sm rounded-lg flex items-center">
            <i class="fa-solid fa-triangle-exclamation mr-2 text-red-600"></i> {{ session('error') }}
        </div>
    @endif

    @php
        // Cek apakah user BUKAN superadmin
        $isNotSuperadmin = auth()->user()->role !== 'superadmin';
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.sekolah.update', $sekolah->id ?? 1) }}" method="POST" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- SECTION 1: IDENTITAS UTAMA -->
            <div>
                <div class="flex items-center text-sm font-bold text-emerald-700 uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-circle-info mr-2"></i> Identitas Utama Sekolah
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">NPSN</label>
                        <input type="text" name="npsn" 
                               value="{{ old('npsn', $sekolah->npsn ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                        <small class="text-xs text-gray-400 mt-1 block">* NPSN diatur oleh Superadmin</small>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" 
                               value="{{ old('nama_sekolah', $sekolah->nama_sekolah ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Jenjang Sekolah</label>
                        <input type="text" name="jenjang" 
                               value="{{ old('jenjang', $sekolah->jenjang ?? '-') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Sekolah</label>
                        <input type="text" name="status" 
                               value="{{ old('status', $sekolah->status ?? '-') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            <!-- SECTION 2: ALAMAT & LOKASI -->
            <div>
                <div class="flex items-center text-sm font-bold text-emerald-700 uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-location-dot mr-2"></i> Alamat & Lokasi
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" 
                                  class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                                  {{ $isNotSuperadmin ? 'readonly' : '' }}>{{ old('alamat', $sekolah->alamat ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Desa / Kelurahan</label>
                        <input type="text" name="desa_kelurahan" 
                               value="{{ old('desa_kelurahan', $sekolah->desa_kelurahan ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" 
                               value="{{ old('kecamatan', $sekolah->kecamatan ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kabupaten / Kota</label>
                        <input type="text" name="kabupaten_kota" 
                               value="{{ old('kabupaten_kota', $sekolah->kabupaten_kota ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Provinsi</label>
                        <input type="text" name="provinsi" 
                               value="{{ old('provinsi', $sekolah->provinsi ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            <!-- SECTION 3: PENANGGUNG JAWAB & KONTAK -->
            <div>
                <div class="flex items-center text-sm font-bold text-emerald-700 uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-address-card mr-2"></i> Penanggung Jawab & Kontak
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Kepala Sekolah & Gelar</label>
                        <input type="text" name="nama_kepsek" 
                               value="{{ old('nama_kepsek', $sekolah->nama_kepsek ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">NIP Kepala Sekolah</label>
                        <input type="text" name="nip_kepsek" 
                               value="{{ old('nip_kepsek', $sekolah->nip_kepsek ?? '') }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $isNotSuperadmin ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '' }}" 
                               {{ $isNotSuperadmin ? 'readonly' : '' }}>
                    </div>

                    <!-- HANYA DUA FIELD INI YANG DAPAT DIEDIT OLEH ADMIN / KEPSEK -->
                    <div>
                        <label class="block text-xs font-semibold text-emerald-700 uppercase mb-1 flex items-center">
                            No. Telepon / WhatsApp <span class="ml-1.5 text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-normal">Dapat Diubah</span>
                        </label>
                        <input type="text" name="telepon" 
                               value="{{ old('telepon', $sekolah->telepon ?? '') }}" 
                               placeholder="Contoh: 08123456789"
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        @error('telepon')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-emerald-700 uppercase mb-1 flex items-center">
                            Email Resmi <span class="ml-1.5 text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-normal">Dapat Diubah</span>
                        </label>
                        <input type="email" name="email" 
                               value="{{ old('email', $sekolah->email ?? '') }}" 
                               placeholder="sekolah@email.com"
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
            <div class="pt-4 border-t border-gray-200 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Perbarui Data Sekolah
                </button>
            </div>
        </form>
    </div>
@endsection