@extends('layouts.admin')

@section('title', 'Tambah Data Guru')
@section('page_title', 'Guru')

@section('content')
<div class="w-full bg-white rounded-xl shadow-sm border p-4 sm:p-6 md:p-8">
    <!-- Header Form -->
    <div class="mb-6 sm:mb-8 border-b pb-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Tambah Data Guru Baru</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Lengkapi data guru berdasarkan data resmi Dukcapil, BKN, dan
            Dapodik.</p>
    </div>

    <form action="{{ route('admin.guru.store') }}" method="POST" class="space-y-6 sm:space-y-8">
        @csrf

        <!-- SECTION 1: IDENTITAS PRIBADI (DUKCAPIL) -->
        <div>
            <div class="flex items-center space-x-2 mb-3 sm:mb-4">
                <span class="w-2 sm:w-2.5 h-5 sm:h-6 bg-blue-600 rounded-full inline-block"></span>
                <h2 class="text-base sm:text-lg font-bold text-gray-800">1. Identitas Pribadi (Validasi Dukcapil)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 bg-gray-50/50 p-4 sm:p-6 rounded-xl border">
                <!-- NIK -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        NIK (16 Digit) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                        placeholder="320XXXXXXXXXXXXXXXX"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('nik') border-red-500 @enderror"
                        required>
                    @error('nik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Nama Lengkap <span class="text-xs text-gray-500 font-normal">(Tanpa Gelar sesuai Akta)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                        placeholder="Contoh: Ahmad Subagja"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('nama_lengkap') border-red-500 @enderror"
                        required>
                    @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Tempat Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                        placeholder="Contoh: Jakarta"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('tempat_lahir') border-red-500 @enderror"
                        required>
                    @error('tempat_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('tanggal_lahir') border-red-500 @enderror"
                        required>
                    @error('tanggal_lahir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_kelamin"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('jenis_kelamin') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Ibu Kandung -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Nama Ibu Kandung <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung') }}"
                        placeholder="Sesuai Akta Kelahiran"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('nama_ibu_kandung') border-red-500 @enderror"
                        required>
                    @error('nama_ibu_kandung') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 2: TUGAS MENGAJAR & JABATAN (PENYESUAIAN KHUSUS) -->
        <div>
            <div class="flex items-center space-x-2 mb-3 sm:mb-4">
                <span class="w-2 sm:w-2.5 h-5 sm:h-6 bg-amber-500 rounded-full inline-block"></span>
                <h2 class="text-base sm:text-lg font-bold text-gray-800">2. Tugas Utama & Mata Pelajaran</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 bg-gray-50/50 p-4 sm:p-6 rounded-xl border">
                <!-- Jenis Tugas / Mata Pelajaran Utama -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Tugas Mengajar / Guru <span class="text-red-500">*</span>
                    </label>
                    <select name="mata_pelajaran"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('mata_pelajaran') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Penugasan Guru --</option>
                        <optgroup label="Guru Kelas SD">
                            <option value="Guru Kelas 1"
                                {{ old('mata_pelajaran') == 'Guru Kelas 1' ? 'selected' : '' }}>Guru Kelas 1</option>
                            <option value="Guru Kelas 2"
                                {{ old('mata_pelajaran') == 'Guru Kelas 2' ? 'selected' : '' }}>Guru Kelas 2</option>
                            <option value="Guru Kelas 3"
                                {{ old('mata_pelajaran') == 'Guru Kelas 3' ? 'selected' : '' }}>Guru Kelas 3</option>
                            <option value="Guru Kelas 4"
                                {{ old('mata_pelajaran') == 'Guru Kelas 4' ? 'selected' : '' }}>Guru Kelas 4</option>
                            <option value="Guru Kelas 5"
                                {{ old('mata_pelajaran') == 'Guru Kelas 5' ? 'selected' : '' }}>Guru Kelas 5</option>
                            <option value="Guru Kelas 6"
                                {{ old('mata_pelajaran') == 'Guru Kelas 6' ? 'selected' : '' }}>Guru Kelas 6</option>
                        </optgroup>
                        <optgroup label="Guru Mata Pelajaran">
                            <option value="Pendidikan Agama Islam"
                                {{ old('mata_pelajaran') == 'Pendidikan Agama Islam' ? 'selected' : '' }}>Guru
                                Pendidikan Agama Islam (PAI)</option>
                            <option value="Pendidikan Agama Kristen"
                                {{ old('mata_pelajaran') == 'Pendidikan Agama Kristen' ? 'selected' : '' }}>Guru
                                Pendidikan Agama Kristen</option>
                            <option value="PJOK" {{ old('mata_pelajaran') == 'PJOK' ? 'selected' : '' }}>Guru PJOK /
                                Olahraga</option>
                            <option value="Bahasa Inggris"
                                {{ old('mata_pelajaran') == 'Bahasa Inggris' ? 'selected' : '' }}>Guru Bahasa Inggris
                            </option>
                        </optgroup>
                    </select>
                    @error('mata_pelajaran') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jabatan Struktural / Tambahan -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Jabatan / Tugas Tambahan
                    </label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                        placeholder="Contoh: Guru Ahli Pertama / Wali Kelas 4A / Pembina Pramuka"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('jabatan') border-red-500 @enderror">
                    @error('jabatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 3: STATUS KEPEGAWAIAN (BKN) -->
        <div>
            <div class="flex items-center space-x-2 mb-3 sm:mb-4">
                <span class="w-2 sm:w-2.5 h-5 sm:h-6 bg-green-600 rounded-full inline-block"></span>
                <h2 class="text-base sm:text-lg font-bold text-gray-800">3. Status Kepegawaian (Database BKN)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 bg-gray-50/50 p-4 sm:p-6 rounded-xl border">
                <!-- NIP -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">NIP (18
                        Digit)</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" maxlength="18"
                        placeholder="1992XXXXXXXXXXXXXXXX"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('nip') border-red-500 @enderror">
                    @error('nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status Kepegawaian -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Status Kepegawaian <span class="text-red-500">*</span>
                    </label>
                    <select name="status_kepegawaian"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('status_kepegawaian') border-red-500 @enderror"
                        required>
                        <option value="PNS" {{ old('status_kepegawaian') == 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK" {{ old('status_kepegawaian') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="GTT" {{ old('status_kepegawaian') == 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak
                            Tetap)</option>
                        <option value="GTY" {{ old('status_kepegawaian') == 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap
                            Yayasan)</option>
                    </select>
                    @error('status_kepegawaian') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Golongan / Ruang -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">Golongan /
                        Ruang</label>
                    <input type="text" name="golongan" value="{{ old('golongan') }}" placeholder="Contoh: III/a atau IX"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('golongan') border-red-500 @enderror">
                    @error('golongan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- TMT SK -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">TMT SK
                        Berjalan</label>
                    <input type="date" name="tmt_sk" value="{{ old('tmt_sk') }}"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('tmt_sk') border-red-500 @enderror">
                    @error('tmt_sk') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Masa Kerja Golongan (MKG) -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">Masa Kerja
                        Golongan (MKG)</label>
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <div>
                            <input type="number" name="mkg_tahun" value="{{ old('mkg_tahun', 0) }}" min="0"
                                placeholder="Tahun"
                                class="w-full px-3 py-2 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm">
                            <span class="text-[10px] sm:text-xs text-gray-500 mt-0.5 block">Tahun</span>
                        </div>
                        <div>
                            <input type="number" name="mkg_bulan" value="{{ old('mkg_bulan', 0) }}" min="0" max="11"
                                placeholder="Bulan"
                                class="w-full px-3 py-2 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm">
                            <span class="text-[10px] sm:text-xs text-gray-500 mt-0.5 block">Bulan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: KUALIFIKASI & SERTIFIKASI (DAPODIK) -->
        <div>
            <div class="flex items-center space-x-2 mb-3 sm:mb-4">
                <span class="w-2 sm:w-2.5 h-5 sm:h-6 bg-purple-600 rounded-full inline-block"></span>
                <h2 class="text-base sm:text-lg font-bold text-gray-800">4. Kualifikasi & Sertifikasi (Dapodik)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 bg-gray-50/50 p-4 sm:p-6 rounded-xl border">
                <!-- Pendidikan Terakhir -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                        Pendidikan Terakhir <span class="text-red-500">*</span>
                    </label>
                    <select name="pendidikan_terakhir"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('pendidikan_terakhir') border-red-500 @enderror"
                        required>
                        <option value="S-1" {{ old('pendidikan_terakhir') == 'S-1' ? 'selected' : '' }}>S-1 / D-4
                        </option>
                        <option value="S-2" {{ old('pendidikan_terakhir') == 'S-2' ? 'selected' : '' }}>S-2</option>
                        <option value="S-3" {{ old('pendidikan_terakhir') == 'S-3' ? 'selected' : '' }}>S-3</option>
                        <option value="D-3" {{ old('pendidikan_terakhir') == 'D-3' ? 'selected' : '' }}>D-3</option>
                    </select>
                    @error('pendidikan_terakhir') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NUPTK -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">NUPTK (16
                        Digit)</label>
                    <input type="text" name="nuptk" value="{{ old('nuptk') }}" maxlength="16"
                        placeholder="16 Digit Nomor NUPTK"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('nuptk') border-red-500 @enderror">
                    @error('nuptk') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nomor Serdik -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">Nomor Sertifikat
                        Pendidik (Serdik)</label>
                    <input type="text" name="no_serdik" value="{{ old('no_serdik') }}" placeholder="Nomor Serdik"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('no_serdik') border-red-500 @enderror">
                    @error('no_serdik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NRG -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">NRG (Nomor
                        Register Guru)</label>
                    <input type="text" name="nrg" value="{{ old('nrg') }}" placeholder="Nomor Register Guru"
                        class="w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-xs sm:text-sm @error('nrg') border-red-500 @enderror">
                    @error('nrg') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- BUTTON ACTION -->
        <div class="pt-4 sm:pt-6 border-t flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
            <a href="{{ route('admin.guru.index') }}"
                class="w-full sm:w-auto text-center px-5 py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-100 transition">
                Batal
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-sm transition">
                Simpan Data Guru
            </button>
        </div>
    </form>
</div>
@endsection