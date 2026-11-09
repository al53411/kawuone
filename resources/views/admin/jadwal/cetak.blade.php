<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jadwal Pelajaran - {{ $kelasAktif }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: landscape;
                margin: 8mm;
            }
        }
        table th, table td {
            font-size: 10.5px;
        }
    </style>
</head>
<body class="bg-white text-slate-900 font-sans p-4 sm:p-6">

    <!-- Tombol Aksi (Hilang saat diprint) -->
    <div class="no-print flex justify-between items-center mb-6 bg-slate-100 p-4 rounded-xl border border-slate-300 shadow-sm">
        <a href="{{ route('admin.jadwal.index', ['kelas' => $kelasAktif]) }}" class="px-4 py-2 bg-slate-700 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition flex items-center space-x-2">
            <span>&larr; Kembali ke Pengaturan</span>
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg shadow hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- HEADER DOKUMEN RESMI -->
    <div class="text-center mb-4">
        <h2 class="text-xl font-extrabold uppercase tracking-wider text-slate-900">
            {{ Auth::user()?->sekolah?->nama_sekolah ?? $profilSekolah?->nama_sekolah ?? 'SEKOLAH' }}
        </h2>
        <p class="text-xs text-slate-600 font-medium">
            {{ $profilSekolah->alamat ?? 'Kecamatan Kedunggalar, Kabupaten Ngawi' }}
        </p>

        <!-- Semester & Tahun Ajaran Dinamis -->
        <h2 class="text-sm font-bold tracking-wide uppercase mt-2 text-slate-800">
            JADWAL PELAJARAN KELAS {{ strtoupper(str_replace('Kelas ', '', $kelasAktif)) }} — SEMESTER {{ strtoupper($tahunAjaranAktif->semester ?? 'GANJIL') }}
        </h2>
        <h3 class="text-xs font-semibold tracking-wide uppercase text-slate-700">
            TAHUN PELAJARAN {{ $tahunAjaranAktif->tahun ?? '2025 / 2026' }}
        </h3>
    </div>

    @php
        // Helper function internal Blade untuk mengecek kategori Mapel (Kolom DB -> Fallback Nama)
        $getKategori = function($m) {
            if (!$m) return 'kbm';
            $kat = $m->kategori ?? null;
            if ($kat) return $kat;

            $nama = strtolower($m->nama_mapel ?? '');
            if (str_contains($nama, 'upacara') || str_contains($nama, 'pembiasaan') || str_contains($nama, 'rutin')) {
                return 'rutin';
            }
            if (str_contains($nama, 'ekstra') || str_contains($nama, 'pramuka') || str_contains($nama, 'drumband') || str_contains($nama, 'tari') || str_contains($nama, 'bta')) {
                return 'ekstra';
            }
            return 'kbm';
        };

        // Palet warna kontras & elegan untuk membedakan tiap mapel
        $palette = [
            'bg-blue-100 text-blue-950 border-blue-300',
            'bg-emerald-100 text-emerald-950 border-emerald-300',
            'bg-sky-100 text-sky-950 border-sky-300',
            'bg-purple-100 text-purple-950 border-purple-300',
            'bg-teal-100 text-teal-950 border-teal-300',
            'bg-cyan-100 text-cyan-950 border-cyan-300',
            'bg-lime-100 text-lime-950 border-lime-300',
            'bg-orange-100 text-orange-950 border-orange-300',
            'bg-fuchsia-100 text-fuchsia-950 border-fuchsia-300',
        ];

        // Buat pemetaan warna dinamis berdasarkan nama/kategori mapel
        $mapelColors = [];
        $kbmIndex = 0;
        foreach($mapels as $m) {
            $kat = $getKategori($m);
            $namaLower = strtolower($m->nama_mapel);

            if ($kat === 'rutin' || str_contains($namaLower, 'upacara')) {
                $mapelColors[$m->nama_mapel] = 'bg-rose-100 text-rose-950 font-semibold border-rose-300';
            } elseif ($kat === 'ekstra') {
                $mapelColors[$m->nama_mapel] = 'bg-indigo-100 text-indigo-950 font-semibold border-indigo-300';
            } elseif (str_contains($namaLower, 'agama') || str_contains($namaLower, 'pai')) {
                $mapelColors[$m->nama_mapel] = 'bg-emerald-200 text-emerald-950 font-semibold border-emerald-400';
            } elseif (str_contains($namaLower, 'pjok') || str_contains($namaLower, 'olahraga')) {
                $mapelColors[$m->nama_mapel] = 'bg-amber-200 text-amber-950 font-semibold border-amber-400';
            } else {
                $mapelColors[$m->nama_mapel] = $palette[$kbmIndex % count($palette)];
                $kbmIndex++;
            }
        }

        // Daftar rentang waktu standar sekolah
        $waktuList = [
            1 => ['07:00', '07:35'],
            2 => ['07:35', '08:10'],
            3 => ['08:10', '08:45'],
            4 => ['08:45', '09:20'],
             ['09:20', '09:40'],
            5 => ['09:40', '10:15'],
            6 => ['10:15', '10:50'],
            7 => ['10:50', '11:25'],
            8 => ['11:25', '12:10'],
            9 => ['12:10', '12:45'],
            10 => ['12:45', '13:20'],
            11 => ['13:20', '13:55'],
            12 => ['13:55', '14:30'],
            13 => ['14:30', '15:00'],
        ];

        $kelasSingkat = strtoupper(str_replace('Kelas ', '', $kelasAktif));

        // Kelompokkan mapel untuk tabel samping
        $mapelsKBM = $mapels->filter(fn($m) => $getKategori($m) === 'kbm');
        $mapelsNonKBM = $mapels->filter(fn($m) => $getKategori($m) !== 'kbm');
    @endphp

    <div class="flex items-start">
        <!-- TABEL UTAMA GRID JADWAL -->
        <div class="flex-1 overflow-x-auto">
            <table class="w-full border-collapse border border-slate-800 text-center">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold">
                        <th class="border border-slate-800 py-1.5 px-1 w-10">KELAS</th>
                        <th class="border border-slate-800 py-1.5 px-1 w-10">JAM</th>
                        <th class="border border-slate-800 py-1.5 px-2 w-28">WAKTU</th>
                        <th class="border border-slate-800 py-1.5 px-2">SENIN</th>
                        <th class="border border-slate-800 py-1.5 px-2">SELASA</th>
                        <th class="border border-slate-800 py-1.5 px-2">RABU</th>
                        <th class="border border-slate-800 py-1.5 px-2">KAMIS</th>
                        <th class="border border-slate-800 py-1.5 px-2">JUMAT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($waktuList as $key => $waktu)
                        @if($key === 'istirahat_1')
                            <tr class="bg-emerald-100 text-emerald-950 font-bold">
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1 font-semibold">{{ $waktu[0] }} - {{ $waktu[1] }}</td>
                                <td colspan="5" class="border border-slate-800 py-1 text-center uppercase tracking-wider">Istirahat I</td>
                            </tr>
                        @elseif($key === 'istirahat_2')
                            <tr class="bg-slate-200 text-slate-900 font-bold">
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1 font-semibold">{{ $waktu[0] }} - {{ $waktu[1] }}</td>
                                <td colspan="5" class="border border-slate-800 py-1 text-center uppercase tracking-wider">Istirahat II / ISOMA</td>
                            </tr>
                        @else
                            <tr>
                                @if($key === 1)
                                    <td rowspan="14" class="border border-slate-800 font-extrabold align-middle bg-slate-100 text-slate-900 text-sm">{{ $kelasSingkat }}</td>
                                @endif
                                <td class="border border-slate-800 py-1 font-bold bg-slate-50">{{ $key }}</td>
                                <td class="border border-slate-800 py-1 font-medium bg-slate-50">{{ $waktu[0] }} - {{ $waktu[1] }}</td>
                                
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    @php
                                        $mapelNama = '';
                                        $colorClass = '';
                                        $cellKey = $hari . '_' . $key;
                                        if(isset($jadwals[$cellKey])) {
                                            $mapelObj = $jadwals[$cellKey]->first()->mapel ?? null;
                                            $mapelNama = $mapelObj->nama_mapel ?? '';
                                            if($mapelNama) {
                                                $colorClass = $mapelColors[$mapelNama] ?? 'bg-slate-100 text-slate-800';
                                            }
                                        }
                                    @endphp
                                    <td class="border border-slate-800 py-1.5 px-1 font-semibold transition-all {{ $colorClass }}">
                                        {{ $mapelNama }}
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- TABEL ALOKASI WAKTU PER MINGGU DI SEBELAH KANAN -->
        <div class="w-72 ml-4 shrink-0">
            <table class="w-full border-collapse border border-slate-800 text-left">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold text-center">
                        <th colspan="2" class="border border-slate-800 py-1.5 px-2 uppercase text-xs">Alokasi Waktu KBM per Minggu</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalJamKBM = 0;
                    @endphp

                    <!-- MATA PELAJARAN KBM UTAMA -->
                    @foreach($mapelsKBM as $mapel)
                        @php
                            $jumlahJam = $alokasis[$mapel->id]->jumlah_jam ?? 0;
                            $totalJamKBM += $jumlahJam;
                            $sideColorClass = $mapelColors[$mapel->nama_mapel] ?? '';
                        @endphp
                        <tr class="{{ $sideColorClass }}">
                            <td class="border border-slate-800 py-1 px-2 font-medium italic">{{ $mapel->nama_mapel }}</td>
                            <td class="border border-slate-800 py-1 px-2 text-right font-bold w-20">{{ $jumlahJam }} Jam</td>
                        </tr>
                    @endforeach

                    <tr class="bg-blue-200 text-blue-950 font-bold border-t-2 border-slate-900">
                        <td class="border border-slate-800 py-1.5 px-2 uppercase text-[10px]">Total Alokasi KBM</td>
                        <td class="border border-slate-800 py-1.5 px-2 text-right border-b-2 border-b-slate-900">{{ $totalJamKBM }} Jam</td>
                    </tr>

                    <!-- KEGIATAN RUTIN & EKSTRAKURIKULER (NON-KBM) -->
                    @if($mapelsNonKBM->count() > 0)
                        <tr class="bg-slate-900 text-white font-bold text-center">
                            <th colspan="2" class="border border-slate-800 py-1 px-2 uppercase text-[10px]">Rutin & Ekstrakurikuler</th>
                        </tr>
                        @foreach($mapelsNonKBM as $mapel)
                            @php
                                $sideColorClass = $mapelColors[$mapel->nama_mapel] ?? 'bg-slate-50 text-slate-800';
                            @endphp
                            <tr class="{{ $sideColorClass }}">
                                <td class="border border-slate-800 py-1 px-2 font-medium italic">{{ $mapel->nama_mapel }}</td>
                                <td class="border border-slate-800 py-1 px-2 text-right font-semibold text-[10px] w-20">Non-KBM</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- CATATAN KAKI -->
    <div class="mt-4 text-[10px] italic space-y-0.5 text-slate-700 border-t border-slate-300 pt-3">
        <p class="font-bold not-italic text-slate-800">Catatan:</p>
        <p>1. Asumsi Mata Pelajaran PA&PB dan PJOK diampu oleh Guru Mata Pelajaran, sehingga jadwal diatur tidak berbenturan waktu antar kelas.</p>
        <p>2. Kegiatan pembelajaran setiap hari Jum'at berakhir pukul 10.50 WIB, untuk semua kelas.</p>
        <p>3. Istirahat ke-2 untuk kegiatan makan siang & sholat disediakan waktu 45 menit.</p>
        <p>4. Kegiatan upacara kalau tidak cukup 35 menit maka sekolah bisa memulai lebih awal, sebelum pk. 07.00 WIB.</p>
        <p>5. Pembiasaan pagi dan ekstrakurikuler sekolah dilaksanakan sesuai alokasi jadwal non-KBM.</p>
    </div>

</body>
</html>