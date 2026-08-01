@extends('layouts.superadmin')

@section('title', 'Edit Data Sekolah')
@section('page_title', 'Edit Sekolah')

@section('content')
<!-- Pembungkus dibuat w-full (100% lebar layar area konten) -->
<div class="w-full">

    <!-- Flash Alert Sukses -->
    @if(session('success'))
        <div class="mb-5 p-4 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Card Form Utama Flat Full Width -->
    <div class="flat-card w-full bg-white border border-slate-200 rounded-md overflow-hidden">
        
        <!-- Header Card Flat -->
        <div class="bg-slate-900 px-6 py-3.5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center space-x-3 text-white">
                <i class="fa-solid fa-pen-to-square text-emerald-400 text-base"></i>
                <h2 class="font-bold text-sm tracking-wide">Edit Data Sekolah: {{ $sekolah->nama_sekolah }}</h2>
            </div>
            <a href="{{ route('superadmin.sekolah.index') }}" class="text-xs text-slate-300 hover:text-white flex items-center gap-1 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('superadmin.sekolah.update', $sekolah->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- SECTION 1: IDENTITAS UTAMA -->
                <div>
                    <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-circle-info"></i> Identitas Utama Sekolah
                    </h3>

                    <!-- Grid 2 Kolom Full Responsive -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NPSN -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">NPSN <span class="text-rose-500">*</span></label>
                            <input type="number" name="npsn" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('npsn') border-rose-500 @enderror" placeholder="Contoh: 20501234" value="{{ old('npsn', $sekolah->npsn) }}" required>
                            @error('npsn') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nama Sekolah -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Sekolah <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_sekolah" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('nama_sekolah') border-rose-500 @enderror" placeholder="Contoh: SDN Kawu 1" value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" required>
                            @error('nama_sekolah') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jenjang Sekolah -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenjang Sekolah <span class="text-rose-500">*</span></label>
                            <select name="jenjang" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('jenjang') border-rose-500 @enderror" required>
                                <option value="" disabled>-- Pilih Jenjang --</option>
                                <option value="SD" {{ old('jenjang', $sekolah->jenjang) == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                <option value="SMP" {{ old('jenjang', $sekolah->jenjang) == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                <option value="SMA" {{ old('jenjang', $sekolah->jenjang) == 'SMA' ? 'selected' : '' }}>SMA / MA</option>
                                <option value="SMK" {{ old('jenjang', $sekolah->jenjang) == 'SMK' ? 'selected' : '' }}>SMK</option>
                            </select>
                            @error('jenjang') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Status Sekolah -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Sekolah <span class="text-rose-500">*</span></label>
                            <select name="status" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('status') border-rose-500 @enderror" required>
                                <option value="" disabled>-- Pilih Status --</option>
                                <option value="Negeri" {{ old('status', $sekolah->status) == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                <option value="Swasta" {{ old('status', $sekolah->status) == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            </select>
                            @error('status') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: ALAMAT & LOKASI -->
                <div>
                    <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-location-dot"></i> Alamat & Lokasi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Jalan / RT / RW <span class="text-rose-500">*</span></label>
                            <textarea name="alamat" rows="2" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm @error('alamat') border-rose-500 @enderror" placeholder="Jl. Raya Kawu No. 01, RT 02/RW 01" required>{{ old('alamat', $sekolah->alamat) }}</textarea>
                            @error('alamat') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Desa / Kelurahan</label>
                            <input type="text" name="desa_kelurahan" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="Kawu" value="{{ old('desa_kelurahan', $sekolah->desa_kelurahan) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kecamatan</label>
                            <input type="text" name="kecamatan" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="Kedungalar" value="{{ old('kecamatan', $sekolah->kecamatan) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kabupaten / Kota</label>
                            <input type="text" name="kabupaten_kota" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="Ngawi" value="{{ old('kabupaten_kota', $sekolah->kabupaten_kota) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Provinsi</label>
                            <input type="text" name="provinsi" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="Jawa Timur" value="{{ old('provinsi', $sekolah->provinsi) }}">
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: PENANGGUNG JAWAB & KONTAK -->
                <div>
                    <h3 class="text-xs font-bold text-emerald-600 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-id-card"></i> Penanggung Jawab & Kontak
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kepala Sekolah & Gelar</label>
                            <input type="text" name="nama_kepsek" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="Drs. Ahmad Dahlan, M.Pd" value="{{ old('nama_kepsek', $sekolah->nama_kepsek) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">NIP Kepala Sekolah (Opsional)</label>
                            <input type="text" name="nip_kepsek" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="19800101 200501 1 001" value="{{ old('nip_kepsek', $sekolah->nip_kepsek) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Telepon / WhatsApp Sekolah</label>
                            <input type="text" name="telepon" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="081234567890" value="{{ old('telepon', $sekolah->telepon) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Resmi Sekolah</label>
                            <input type="email" name="email" class="w-full flat-input px-3 py-2 border border-slate-300 rounded focus:border-emerald-500 focus:bg-white text-sm" placeholder="sdnkawu1@sch.id" value="{{ old('email', $sekolah->email) }}">
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                    <a href="{{ route('superadmin.sekolah.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded transition flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Update Data Sekolah
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection