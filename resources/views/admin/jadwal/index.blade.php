@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Pelajaran')
@section('page_title', 'Jadwal Pelajaran')

@section('content')
    <div class="space-y-6">

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
                    <p class="text-xs text-slate-500 mt-0.5">Kelola alokasi waktu dan input sesi jadwal per kelas secara
                        manual.</p>
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
                        <i class="fa-solid fa-clock text-blue-600 mr-2"></i> 1. Target Alokasi Waktu per Minggu
                        ({{ $kelasAktif }})
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan jumlah jam pelajaran untuk setiap mata pelajaran dalam
                        seminggu.</p>
                </div>

                <!-- Indikator Total Jam Otomatis -->
                <div class="flex items-center space-x-3 bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg">
                    <div class="text-right">
                        <span class="block text-[10px] uppercase font-bold text-blue-600 tracking-wider">Total Alokasi
                            Minggu Ini</span>
                        <span id="total-jam-badge" class="text-base font-extrabold text-blue-800">0 Jam</span>
                    </div>
                    <div class="h-8 w-px bg-blue-200"></div>
                    <span class="text-[11px] text-blue-600 font-medium">Target Normal: <strong class="text-blue-900">40
                            Jam</strong></span>
                </div>
            </div>

            <!-- Form Pintasan Alokasi Massal (Kecuali PAI & PJOK) -->
            <form action="{{ route('admin.jadwal.alokasi-massal') }}" method="POST"
                class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-4">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelasAktif }}">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-semibold text-slate-700">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1"></i> Set Default Jam Mapel Lainnya:
                    </span>
                    <input type="number" name="default_jam" value="4" min="1" max="10"
                        class="flat-input w-16 text-center py-1 font-semibold text-slate-700" required>
                    <span class="text-xs text-slate-500">Jam/minggu (Kecuali Agama & Olahraga)</span>
                </div>
                <button type="submit"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded transition">
                    Terapkan Massal
                </button>
            </form>

            <form action="{{ route('admin.jadwal.alokasi') }}" method="POST" id="form-alokasi">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelasAktif }}">

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
                    @foreach($mapels as $mapel)
                        @php
                            $namaLOWER = strtolower($mapel->nama_mapel);
                            $isKhusus = str_contains($namaLOWER, 'agama') || str_contains($namaLOWER, 'pai') || str_contains($namaLOWER, 'pjok') || str_contains($namaLOWER, 'olahraga');
                        @endphp
                        <div
                            class="bg-slate-50 border {{ $isKhusus ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200' }} p-3 rounded-lg">
                            <label
                                class="block text-[11px] font-bold {{ $isKhusus ? 'text-amber-800' : 'text-slate-700' }} truncate mb-1"
                                title="{{ $mapel->nama_mapel }}">
                                {{ $mapel->nama_mapel }} @if($isKhusus) <span
                                class="text-[9px] text-amber-600 font-normal">(Khusus)</span> @endif
                            </label>
                            <div class="flex items-center space-x-1">
                                <input type="number" name="alokasi[{{ $mapel->id }}]"
                                    value="{{ $alokasis[$mapel->id]->jumlah_jam ?? 0 }}" min="0"
                                    class="flat-input w-full text-center py-1.5 font-semibold text-blue-600 input-alokasi-jam"
                                    required>
                                <span class="text-[11px] text-slate-500 shrink-0">Jam</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Indikator & Target Total Jam Dinamis -->
                <div class="flex items-center space-x-3 bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg">
                    <div class="text-right">
                        <span class="block text-[10px] uppercase font-bold text-blue-600 tracking-wider">Total Alokasi
                            Minggu Ini</span>
                        <span id="total-jam-badge" class="text-base font-extrabold text-blue-800">0 Jam</span>
                    </div>
                    <div class="h-8 w-px bg-blue-200"></div>
                    <div class="flex items-center space-x-1">
                        <span class="text-[11px] text-blue-600 font-medium">Target Jam:</span>
                        <!-- Input dinamis untuk menentukan target total jam per minggu -->
                        <input type="number" id="target-total-jam" value="40" min="1" max="60"
                            class="w-14 text-center text-xs font-extrabold text-blue-900 bg-white border border-blue-300 rounded py-0.5 px-1 focus:ring-1 focus:ring-blue-500">
                        <span class="text-[11px] text-blue-600 font-medium">Jam</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Script JavaScript untuk Menghitung Total Jam Secara Real-Time -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputJamList = document.querySelectorAll('.input-alokasi-jam');
                const badgeTotal = document.getElementById('total-jam-badge');
                const warningTotal = document.getElementById('warning-total');
                const inputTargetTotal = document.getElementById('target-total-jam');

                function hitungTotalJam() {
                    let total = 0;
                    inputJamList.forEach(input => {
                        let val = parseInt(input.value) || 0;
                        total += val;
                    });

                    // Ambil target dinamis dari input
                    let targetNormal = parseInt(inputTargetTotal.value) || 40;

                    badgeTotal.textContent = total + ' Jam';

                    if (total !== targetNormal) {
                        badgeTotal.className = 'text-base font-extrabold text-amber-600';
                        warningTotal.classList.remove('hidden');
                        if (total < targetNormal) {
                            warningTotal.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1"></i> Total saat ini ${total} Jam (Kurang ${targetNormal - total} jam dari target ${targetNormal} jam).`;
                        } else {
                            warningTotal.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1"></i> Total saat ini ${total} Jam (Lebih ${total - targetNormal} jam dari target ${targetNormal} jam).`;
                        }
                    } else {
                        badgeTotal.className = 'text-base font-extrabold text-emerald-600';
                        warningTotal.classList.add('hidden');
                    }
                }

                // Jalankan saat halaman dimuat
                hitungTotalJam();

                // Jalankan setiap kali input alokasi atau target total diubah
                inputJamList.forEach(input => {
                    input.addEventListener('input', hitungTotalJam);
                });

                if (inputTargetTotal) {
                    inputTargetTotal.addEventListener('input', hitungTotalJam);
                }
            });
        </script>

        <!-- 2. BAGIAN INPUT MANUAL SESI JADWAL -->
        <div class="bg-white flat-card shadow-sm p-5">
            <div class="border-b border-slate-200 pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center">
                    <i class="fa-solid fa-plus-circle text-emerald-600 mr-2"></i> 2. Tambah Sesi Jadwal Pelajaran
                    ({{ $kelasAktif }})
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Masukkan sesi jadwal per jam dan hari secara fleksibel.</p>
            </div>

            <form action="{{ route('admin.jadwal.store') }}" method="POST"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelasAktif }}">

                <!-- Ubah dari input text menjadi dropdown guru / wali kelas -->
                <!-- Dropdown Guru / Wali Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Wali Kelas / Guru:</label>
                    <select name="wali_kelas"
                        class="flat-input w-full px-3 py-2 font-medium text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500"
                        required>
                        <option value="" class="text-slate-500">-- Pilih Guru / Wali Kelas --</option>
                        @foreach($gurus ?? [] as $guru)
                            @php 
                                                                                                                                // Menyesuaikan dengan kolom nama di database Anda
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
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
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

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mata Pelajaran:</label>
                    <select name="mapel_id" class="flat-input w-full px-3 py-2" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                        @endforeach
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
        <div class="bg-white flat-card shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-800">Daftar Sesi Jadwal Terinput ({{ $kelasAktif }})</h3>
            </div>
            <div class="overflow-x-auto p-5">
                <table class="w-full border-collapse border border-slate-200 text-left text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="border border-slate-700 py-2.5 px-3 w-12 text-center">No</th>
                            <th class="border border-slate-700 py-2.5 px-3">Hari</th>
                            <th class="border border-slate-700 py-2.5 px-3">Jam Ke-</th>
                            <th class="border border-slate-700 py-2.5 px-3">Mata Pelajaran</th>
                            <th class="border border-slate-700 py-2.5 px-3">Wali Kelas</th>
                            <th class="border border-slate-700 py-2.5 px-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($jadwalsList ?? [] as $index => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="border border-slate-200 py-2.5 px-3 text-center">{{ $index + 1 }}</td>
                                <td class="border border-slate-200 py-2.5 px-3 font-bold text-slate-700">{{ $item->hari }}</td>
                                <td class="border border-slate-200 py-2.5 px-3">Jam ke-{{ $item->jam_ke }}</td>
                                <td class="border border-slate-200 py-2.5 px-3 font-semibold text-blue-600">
                                    {{ $item->mapel->nama_mapel ?? '-' }}
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

    </div>
@endsection