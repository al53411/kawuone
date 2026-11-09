@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Pelajaran')
@section('page_title', 'Jadwal Pelajaran')

@section('content')
    <div class="space-y-6">

        <!-- ALERT NOTIFIKASI SUKSES / ERROR / WARNING -->
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg mr-3"></i>
                    <p class="text-xs font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mr-3"></i>
                    <p class="text-xs font-bold text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation text-amber-600 text-lg mr-3"></i>
                    <p class="text-xs font-bold text-amber-800">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fa-solid fa-circle-exclamation text-amber-600 text-lg mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-amber-800">Perhatikan kesalahan input berikut:</p>
                        <ul class="list-disc list-inside text-xs text-amber-700 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @php
            // Helper function internal Blade untuk mengecek kategori Mapel (Kolom DB -> Fallback Nama)
            $getKategori = function ($m) {
                if (!$m)
                    return 'kbm';
                $kat = $m->kategori ?? null;
                if ($kat)
                    return $kat;

                $nama = strtolower($m->nama_mapel ?? '');
                if (str_contains($nama, 'upacara') || str_contains($nama, 'pembiasaan') || str_contains($nama, 'rutin')) {
                    return 'rutin';
                }
                if (str_contains($nama, 'ekstra') || str_contains($nama, 'pramuka') || str_contains($nama, 'drumband') || str_contains($nama, 'tari') || str_contains($nama, 'bta')) {
                    return 'ekstra';
                }
                return 'kbm';
            };

            // Filter Mapel Berdasarkan Kategori
            $mapelsKBM = $mapels->filter(fn($m) => $getKategori($m) === 'kbm');
            $mapelsRutin = $mapels->filter(fn($m) => $getKategori($m) === 'rutin');
            $mapelsEkstra = $mapels->filter(fn($m) => $getKategori($m) === 'ekstra');
        @endphp

        <!-- Header & Aksi Preview / Cetak & Filter Kelas -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 flat-card shadow-sm">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.jadwal.cetak', ['kelas' => $kelasAktif]) }}" target="_blank"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Preview / Cetak Jadwal Resmi</span>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Pengaturan Jadwal Pelajaran</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola alokasi KBM, kegiatan rutin pembiasaan, serta jadwal
                        ekstrakurikuler sekolah.</p>
                </div>
            </div>
            <div>
                <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex items-center space-x-2">
                    <label class="text-xs font-semibold text-slate-600">Pilih Kelas:</label>
                    <select name="kelas" onchange="this.form.submit()"
                        class="flat-input px-3 py-2 font-medium text-slate-700">
                        @foreach(['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'] as $kls)
                            <option value="{{ $kls }}" {{ $kelasAktif == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- 1. BAGIAN ALOKASI WAKTU PER MINGGU -->
        <div class="bg-white flat-card shadow-sm p-5 mb-6">
            <div
                class="border-b border-slate-200 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <i class="fa-solid fa-clock text-blue-600 mr-2"></i> 1. Target Alokasi Waktu KBM per Minggu
                        ({{ $kelasAktif }})
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan target alokasi jam pelajaran mingguan khusus mata
                        pelajaran KBM.</p>
                </div>

                <!-- Indikator & Target Total Jam Dinamis -->
                <div class="flex items-center space-x-3 bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg">
                    <div class="text-right">
                        <span class="block text-[10px] uppercase font-bold text-blue-600 tracking-wider">Total Alokasi
                            KBM</span>
                        <span id="total-jam-badge" class="text-base font-extrabold text-blue-800">0 Jam</span>
                    </div>
                    <div class="h-8 w-px bg-blue-200"></div>
                    <div class="flex items-center space-x-1">
                        <span class="text-[11px] text-blue-600 font-medium">Target Jam:</span>
                        <input type="number" id="target-total-jam" value="{{ $totalTargetJam > 0 ? $totalTargetJam : 40 }}"
                            min="1" max="60"
                            class="w-14 text-center text-xs font-extrabold text-blue-900 bg-white border border-blue-300 rounded py-0.5 px-1 focus:ring-1 focus:ring-blue-500">
                        <span class="text-[11px] text-blue-600 font-medium">Jam</span>
                    </div>
                </div>
            </div>

            <!-- Form Pintasan Alokasi Massal -->
            <form action="{{ route('admin.jadwal.alokasi-massal') }}" method="POST"
                class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-5">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelasAktif }}">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-semibold text-slate-700">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1"></i> Set Default Jam Mapel KBM
                        Lainnya:
                    </span>
                    <input type="number" name="default_jam" value="4" min="1" max="10"
                        class="flat-input w-16 text-center py-1 font-semibold text-slate-700" required>
                    <span class="text-xs text-slate-500">Jam/minggu (Kecuali Agama, Olahraga, & Kegiatan Rutin)</span>
                </div>
                <button type="submit"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded transition">
                    Terapkan Massal
                </button>
            </form>

            <!-- Form Utama Alokasi Waktu -->
            <form action="{{ route('admin.jadwal.alokasi') }}" method="POST" id="form-alokasi">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelasAktif }}">

                <!-- GRID A: MATA PELAJARAN KBM UTAMA -->
                <div class="mb-5">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                        <i class="fa-solid fa-book-open text-indigo-600 mr-1.5"></i> Mata Pelajaran KBM
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($mapelsKBM as $mapel)
                            @php
                                $namaLOWER = strtolower($mapel->nama_mapel);
                                $isKhusus = str_contains($namaLOWER, 'agama') || str_contains($namaLOWER, 'pai') || str_contains($namaLOWER, 'pjok') || str_contains($namaLOWER, 'olahraga');
                            @endphp
                            <div
                                class="bg-slate-50 border {{ $isKhusus ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200' }} p-3 rounded-lg">
                                <label
                                    class="block text-[11px] font-bold {{ $isKhusus ? 'text-amber-800' : 'text-slate-700' }} truncate mb-1"
                                    title="{{ $mapel->nama_mapel }}">
                                    {{ $mapel->nama_mapel }}
                                    @if($isKhusus)
                                        <span class="text-[9px] text-amber-600 font-normal">(Khusus)</span>
                                    @endif
                                </label>
                                <div class="flex items-center space-x-1">
                                    <input type="number" name="alokasi[{{ $mapel->id }}]"
                                        value="{{ $alokasis[$mapel->id]->jumlah_jam ?? 0 }}" min="0" max="40" data-hitung="true"
                                        class="flat-input w-full text-center py-1.5 font-semibold text-blue-600 input-alokasi-jam"
                                        required>
                                    <span class="text-[11px] text-slate-500 shrink-0">Jam</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SEKSI TERPISAH: KEGIATAN RUTIN, PEMBIASAAN, & LAINNYA -->
                @if($mapelsRutin->count() > 0)
                    <div class="bg-amber-50/50 border border-amber-200 p-4 rounded-xl mb-5">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center">
                                    <i class="fa-solid fa-flag text-amber-600 mr-1.5"></i> Kegiatan Rutin, Pembiasaan & Non-KBM
                                </h4>
                                <p class="text-[11px] text-amber-700 mt-0.5">Kegiatan ini bersifat rutin/pembiasaan sekolah dan
                                    tidak dihitung ke dalam kuota alokasi KBM.</p>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-200/60 text-amber-900 text-[10px] font-bold rounded-full">Bebas
                                Kuota</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($mapelsRutin as $mapel)
                                <div class="bg-white border border-amber-300 p-3 rounded-lg shadow-2xs">
                                    <label class="block text-[11px] font-bold text-amber-900 truncate mb-1"
                                        title="{{ $mapel->nama_mapel }}">
                                        {{ $mapel->nama_mapel }}
                                    </label>
                                    <div class="flex items-center space-x-1">
                                        <input type="number" name="alokasi[{{ $mapel->id }}]" value="0" readonly data-hitung="false"
                                            class="flat-input w-full text-center py-1.5 font-semibold bg-slate-100 text-slate-400 cursor-not-allowed">
                                        <span class="text-[11px] text-slate-400 shrink-0">Jam</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <p id="warning-total" class="text-xs font-medium text-rose-600 hidden">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Peringatan: Total jam belum mencapai target!
                    </p>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-2 ml-auto">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Alokasi</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Script Real-Time Total Jam -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputJamList = document.querySelectorAll('.input-alokasi-jam');
                const badgeTotal = document.getElementById('total-jam-badge');
                const warningTotal = document.getElementById('warning-total');
                const inputTargetTotal = document.getElementById('target-total-jam');

                function hitungTotalJam() {
                    let total = 0;
                    inputJamList.forEach(input => {
                        if (input.dataset.hitung === 'true') {
                            let val = parseInt(input.value) || 0;
                            total += val;
                        }
                    });

                    let targetNormal = parseInt(inputTargetTotal.value) || 40;
                    badgeTotal.textContent = total + ' Jam';

                    if (total !== targetNormal) {
                        badgeTotal.className = 'text-base font-extrabold text-amber-600';
                        warningTotal.classList.remove('hidden');
                        if (total < targetNormal) {
                            warningTotal.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1"></i> Total alokasi KBM saat ini ${total} Jam (Kurang ${targetNormal - total} jam dari target ${targetNormal} jam).`;
                        } else {
                            warningTotal.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1"></i> Total alokasi KBM saat ini ${total} Jam (Lebih ${total - targetNormal} jam dari target ${targetNormal} jam).`;
                        }
                    } else {
                        badgeTotal.className = 'text-base font-extrabold text-emerald-600';
                        warningTotal.classList.add('hidden');
                    }
                }

                hitungTotalJam();

                inputJamList.forEach(input => {
                    input.addEventListener('input', hitungTotalJam);
                });

                if (inputTargetTotal) {
                    inputTargetTotal.addEventListener('input', hitungTotalJam);
                }
            });
        </script>

        <!-- 2. BAGIAN INPUT MANUAL SESI JADWAL KBM / KEGIATAN LAIN -->
        <div class="bg-white flat-card shadow-sm p-5">
            <div
                class="border-b border-slate-200 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <i class="fa-solid fa-plus-circle text-emerald-600 mr-2"></i> 2. Tambah Sesi Jadwal Pelajaran /
                        Kegiatan ({{ $kelasAktif }})
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih mata pelajaran KBM, kegiatan pembiasaan, atau
                        ekstrakurikuler.</p>
                </div>
                <div class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700">
                    Sesi Terisi (KBM): <span class="text-emerald-700 font-bold">{{ $totalJadwalTerisi ?? 0 }}</span> /
                    Target: <span class="text-blue-700 font-bold">{{ $totalTargetJam ?? 0 }} Jam</span>
                </div>
            </div>

            <form action="{{ route('admin.jadwal.store') }}" method="POST"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelasAktif }}">

                <!-- Dropdown Guru / Wali Kelas / Pembina -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Guru / Wali Kelas / Pembina:</label>
                    <select name="wali_kelas"
                        class="flat-input w-full px-3 py-2 font-medium text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500"
                        required>
                        <option value="" class="text-slate-500">-- Pilih Guru / Pembina --</option>
                        @foreach($gurus ?? [] as $guru)
                            @php 
                                $namaGuru = $guru->nama_lengkap ?? $guru->nama ?? $guru->name ?? '-'; 
                            @endphp
                            <option value="{{ $namaGuru }}" class="text-slate-900 bg-white">{{ $namaGuru }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Hari:</label>
                    <select name="hari" class="flat-input w-full px-3 py-2" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                            <option value="{{ $hari }}">{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Pelajaran Ke-:</label>
                    <select name="jam_ke" class="flat-input w-full px-3 py-2" required>
                        <option value="">-- Pilih Jam ke- --</option>
                        @for($j = 1; $j <= 12; $j++)
                            <option value="{{ $j }}">Jam ke-{{ $j }}</option>
                        @endfor
                    </select>
                </div>

                <!-- DROPDOWN DIKELOMPOKKAN SESUAI KATEGORI -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mata Pelajaran / Sesi Kegiatan:</label>
                    <select name="mapel_id" class="flat-input w-full px-3 py-2" required>
                        <option value="">-- Pilih Mapel / Kegiatan --</option>

                        @if($mapelsKBM->count() > 0)
                            <optgroup label="--- MATA PELAJARAN KBM ---">
                                @foreach($mapelsKBM as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($mapelsRutin->count() > 0)
                            <optgroup label="--- KEGIATAN RUTIN & PEMBIASAAN ---">
                                @foreach($mapelsRutin as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($mapelsEkstra->count() > 0)
                            <optgroup label="--- EKSTRAKURIKULER ---">
                                @foreach($mapelsEkstra as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-circle-plus"></i>
                        <span>Tambah Sesi</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. TABEL DAFTAR SESI JADWAL YANG SUDAH DIINPUT -->
        <div class="bg-white flat-card shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Daftar Sesi Jadwal Terinput ({{ $kelasAktif }})</h3>
                <span class="text-xs text-slate-500 font-medium">Total Terinput: {{ count($jadwalsList ?? []) }} Sesi</span>
            </div>
            <div class="overflow-x-auto p-5">
                <table class="w-full border-collapse border border-slate-200 text-left text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="border border-slate-700 py-2.5 px-3 w-12 text-center">No</th>
                            <th class="border border-slate-700 py-2.5 px-3">Hari</th>
                            <th class="border border-slate-700 py-2.5 px-3">Jam Ke-</th>
                            <th class="border border-slate-700 py-2.5 px-3">Mata Pelajaran / Kegiatan</th>
                            <th class="border border-slate-700 py-2.5 px-3">Wali Kelas / Pengampu</th>
                            <th class="border border-slate-700 py-2.5 px-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($jadwalsList ?? [] as $index => $item)
                            @php
                                $katRow = $getKategori($item->mapel);
                                $isRutinRow = ($katRow === 'rutin');
                                $isEkstraRow = ($katRow === 'ekstra');
                            @endphp
                            <tr
                                class="hover:bg-slate-50 transition {{ $isRutinRow ? 'bg-amber-50/30' : ($isEkstraRow ? 'bg-indigo-50/30' : '') }}">
                                <td class="border border-slate-200 py-2.5 px-3 text-center">{{ $index + 1 }}</td>
                                <td class="border border-slate-200 py-2.5 px-3 font-bold text-slate-700">{{ $item->hari }}</td>
                                <td class="border border-slate-200 py-2.5 px-3">Jam ke-{{ $item->jam_ke }}</td>
                                <td
                                    class="border border-slate-200 py-2.5 px-3 font-semibold {{ $isRutinRow ? 'text-amber-700' : ($isEkstraRow ? 'text-indigo-700' : 'text-blue-600') }}">
                                    {{ $item->mapel->nama_mapel ?? '-' }}
                                    @if($isRutinRow)
                                        <span
                                            class="ml-1 px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[9px] rounded font-bold">Rutin</span>
                                    @elseif($isEkstraRow)
                                        <span
                                            class="ml-1 px-1.5 py-0.5 bg-indigo-100 text-indigo-800 text-[9px] rounded font-bold">Ekstra</span>
                                    @endif
                                </td>
                                <td class="border border-slate-200 py-2.5 px-3 text-slate-600">{{ $item->wali_kelas }}</td>
                                <td class="border border-slate-200 py-2.5 px-3 text-center">
                                    <form action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus sesi jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded text-[11px] transition">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border border-slate-200 py-6 text-center text-slate-400 italic">
                                    Belum ada sesi jadwal yang diinput untuk kelas ini. Silakan gunakan form di atas untuk
                                    menambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. SEKSI JADWAL EKSTRAKURIKULER SEKOLAH (DI BAGIAN BAWAH) -->
        <div class="bg-white flat-card shadow-sm p-5 border-t-4 border-indigo-600">
            <div
                class="border-b border-slate-200 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <i class="fa-solid fa-puzzle-piece text-indigo-600 mr-2 text-base"></i> 4. Jadwal Ekstrakurikuler
                        Sekolah
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar kegiatan pengembangan diri dan ekstrakurikuler peserta
                        didik.</p>
                </div>
                <span
                    class="px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold rounded-lg">
                    <i class="fa-solid fa-star mr-1 text-amber-500"></i> Kegiatan Ekstra
                </span>
            </div>

            <!-- TABEL / CARD JADWAL EKSTRAKURIKULER -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    // Filter sesi ekstrakurikuler yang sudah terinput di kelas ini menggunakan helper $getKategori
                    $jadwalEkstraInput = $jadwalsList->filter(function ($item) use ($getKategori) {
                        return $getKategori($item->mapel) === 'ekstra';
                    });
                @endphp

                @forelse($jadwalEkstraInput as $ekstra)
                    <div
                        class="bg-indigo-50/40 border border-indigo-200 rounded-xl p-4 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-md">
                                    {{ $ekstra->hari }}
                                </span>
                                <span class="text-xs font-semibold text-slate-500">Jam ke-{{ $ekstra->jam_ke }}</span>
                            </div>
                            <h4 class="text-sm font-extrabold text-indigo-900 mb-1">
                                {{ $ekstra->mapel->nama_mapel ?? 'Ekstrakurikuler' }}
                            </h4>
                            <p class="text-xs text-slate-600 flex items-center mt-2">
                                <i class="fa-solid fa-user-tie text-indigo-500 mr-2 w-4"></i>
                                <span>Pembina: <strong>{{ $ekstra->wali_kelas }}</strong></span>
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-indigo-100 flex items-center justify-between">
                            <span class="text-[11px] text-indigo-700 font-medium">Sesi Ekstra Aktif</span>
                            <form action="{{ route('admin.jadwal.destroy', $ekstra->id) }}" method="POST"
                                onsubmit="return confirm('Hapus jadwal ekstrakurikuler ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-slate-50 border border-dashed border-slate-300 rounded-xl p-6 text-center">
                        <i class="fa-solid fa-icons text-slate-300 text-3xl mb-2"></i>
                        <p class="text-xs font-medium text-slate-500">Belum ada sesi ekstrakurikuler yang ditambahkan pada kelas
                            ini.</p>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih mata pelajaran bertema Ekstrakurikuler pada Bagian 2 di
                            atas untuk menambahkan secara langsung.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection