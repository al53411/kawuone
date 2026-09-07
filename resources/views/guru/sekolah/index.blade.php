@extends('layouts.guru')

@section('title', 'Profil Sekolah')
@section('page_title', 'Profil Sekolah')

@section('content')
    <!-- HEADER SECTION -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Identitas & Profil Sekolah</h1>
            <p class="text-sm text-slate-500">Informasi identitas resmi dan kontak operasional sekolah.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                <span class="w-2 h-2 mr-2 bg-emerald-500 rounded-full animate-pulse"></span> Status: Aktif Terdaftar
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Menggunakan div biasa karena murni untuk dilihat (read-only) -->
        <div class="p-6 md:p-8 space-y-8">

            <!-- SECTION 1: IDENTITAS UTAMA -->
            <div>
                <div class="flex items-center text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-school mr-2 text-sm"></i> Identitas Utama Sekolah
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">NPSN</label>
                        <input type="text" 
                               value="{{ $sekolah->npsn ?? '-' }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" 
                               disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Sekolah</label>
                        <input type="text" 
                               value="{{ $sekolah->nama_sekolah ?? '-' }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" 
                               disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jenjang Sekolah</label>
                        <input type="text" 
                               value="{{ $sekolah->jenjang ?? 'SD' }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" 
                               disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status Sekolah</label>
                        <input type="text" 
                               value="{{ $sekolah->status ?? 'Negeri' }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" 
                               disabled>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: ALAMAT & LOKASI -->
            <div>
                <div class="flex items-center text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-location-dot mr-2 text-sm"></i> Alamat & Wilayah
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Alamat Jalan</label>
                        <textarea rows="2" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>{{ $sekolah->alamat ?? '-' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Desa / Kelurahan</label>
                        <input type="text" value="{{ $sekolah->desa_kelurahan ?? '-' }}" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kecamatan</label>
                        <input type="text" value="{{ $sekolah->kecamatan ?? '-' }}" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kabupaten / Kota</label>
                        <input type="text" value="{{ $sekolah->kabupaten_kota ?? '-' }}" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Provinsi</label>
                        <input type="text" value="{{ $sekolah->provinsi ?? '-' }}" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: PENANGGUNG JAWAB & KONTAK -->
            <div>
                <div class="flex items-center text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-address-card mr-2 text-sm"></i> Penanggung Jawab & Kontak Resmi
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kepala Sekolah</label>
                        <input type="text" value="{{ $sekolah->nama_kepsek ?? '-' }}" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">NIP Kepala Sekolah</label>
                        <input type="text" value="{{ $sekolah->nip_kepsek ?? '-' }}" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">No. Telepon / WhatsApp</label>
                        <input type="text" 
                               value="{{ $sekolah->telepon ?? '-' }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" 
                               disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Email Resmi</label>
                        <input type="text" 
                               value="{{ $sekolah->email ?? '-' }}" 
                               class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 bg-slate-100 text-slate-600 cursor-not-allowed" 
                               disabled>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection