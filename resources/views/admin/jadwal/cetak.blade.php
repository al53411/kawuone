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
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: landscape;
                margin: 10mm;
            }
        }
        table th, table td {
            font-size: 11px;
        }
    </style>
</head>
<body class="bg-white text-slate-900 font-sans p-6">

    <!-- Tombol Aksi (Hilang saat diprint) -->
    <div class="no-print flex justify-between items-center mb-6 bg-slate-100 p-4 rounded-lg border border-slate-300">
        <a href="{{ route('admin.jadwal.index', ['kelas' => $kelasAktif]) }}" class="px-4 py-2 bg-slate-700 text-white text-xs font-bold rounded hover:bg-slate-800 transition">
            &larr; Kembali ke Pengaturan
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white text-xs font-bold rounded shadow hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- HEADER DOKUMEN RESMI -->
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold uppercase tracking-wider text-slate-900">
            {{ Auth::user()?->sekolah?->nama_sekolah ?? $profilSekolah?->nama_sekolah ?? 'Sekolah' }}.
        </h2>
        <p class="text-xs text-slate-600">
            {{ $profilSekolah->alamat ?? 'Kecamatan Kedunggalar, Kabupaten Ngawi' }}
        </p>

        <!-- Semester Dinamis -->
        <h2 class="text-sm font-bold tracking-wide uppercase mt-2">
            KELAS {{ strtoupper(str_replace('Kelas ', '', $kelasAktif)) }} 
            SEMESTER {{ strtoupper($tahunAjaranAktif->semester ?? 'GANJIL') }}
        </h2>

        <!-- Tahun Pelajaran Dinamis -->
        <h3 class="text-sm font-bold tracking-wide uppercase">
            TAHUN PELAJARAN {{ $tahunAjaranAktif->tahun ?? '2025 / 2026' }}
        </h3>
    </div>

    <div class="flex">
        <!-- TABEL UTAMA GRID JADWAL -->
        <div class="flex-1 overflow-x-auto">
            <table class="w-full border-collapse border border-slate-800 text-center">
                <thead>
                    <tr class="bg-slate-200 text-slate-900 font-bold">
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
                    @php
                        // Daftar rentang waktu standar sekolah
                        $waktuList = [
                            1 => ['07:00', '07:35'],
                            2 => ['07:35', '08:10'],
                            3 => ['08:10', '08:45'],
                            4 => ['08:45', '09:20'],
                            'istirahat_1' => ['09:20', '09:40'],
                            5 => ['09:40', '10:15'],
                            6 => ['10:15', '10:50'],
                            7 => ['10:50', '11:25'],
                            'istirahat_2' => ['11:25', '12:10'],
                            8 => ['12:10', '12:45'],
                            9 => ['12:45', '13:20'],
                            10 => ['13:20', '13:55'],
                            11 => ['13:55', '14:30'],
                            12 => ['14:30', '15:00'],
                        ];

                        $kelasSingkat = strtoupper(str_replace('Kelas ', '', $kelasAktif));
                    @endphp

                    @foreach($waktuList as $key => $waktu)
                        @if($key === 'istirahat_1')
                            <tr class="bg-emerald-100 font-semibold">
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1">{{ $waktu[0] }} - {{ $waktu[1] }}</td>
                                <td colspan="5" class="border border-slate-800 py-1 text-center">Istirahat</td>
                            </tr>
                        @elseif($key === 'istirahat_2')
                            <tr class="bg-slate-200 font-semibold">
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1"></td>
                                <td class="border border-slate-800 py-1">{{ $waktu[0] }} - {{ $waktu[1] }}</td>
                                <td colspan="5" class="border border-slate-800 py-1 text-center">Istirahat / Sholat / Makan Siang</td>
                            </tr>
                        @else
                            <tr>
                                @if($key === 1)
                                    <td rowspan="14" class="border border-slate-800 font-bold align-middle bg-slate-50">{{ $kelasSingkat }}</td>
                                @endif
                                <td class="border border-slate-800 py-1 font-semibold">{{ $key }}</td>
                                <td class="border border-slate-800 py-1">{{ $waktu[0] }} - {{ $waktu[1] }}</td>
                                
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    @php
                                        $mapelNama = '';
                                        $cellKey = $hari . '_' . $key;
                                        if(isset($jadwals[$cellKey])) {
                                            $mapelNama = $jadwals[$cellKey]->first()->mapel->nama_mapel ?? '';
                                        }
                                    @endphp
                                    <td class="border border-slate-800 py-1 font-medium">
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
                    <tr class="bg-slate-200 text-slate-900 font-bold text-center">
                        <th colspan="2" class="border border-slate-800 py-1.5 px-2">ALOKASI WAKTU PER MINGGU</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalJamAll = 0;
                    @endphp
                    @foreach($mapels as $mapel)
                        @php
                            $jumlahJam = $alokasis[$mapel->id]->jumlah_jam ?? 0;
                            $totalJamAll += $jumlahJam;
                        @endphp
                        <tr>
                            <td class="border border-slate-800 py-1 px-2 italic">{{ $mapel->nama_mapel }}</td>
                            <td class="border border-slate-800 py-1 px-2 text-right w-20">{{ $jumlahJam }} Jam</td>
                        </tr>
                    @endforeach
                    <tr class="bg-orange-100 font-bold">
                        <td class="border border-slate-800 py-1.5 px-2">JML ALOKASI WAKTU</td>
                        <td class="border border-slate-800 py-1.5 px-2 text-right border-b-2 border-b-slate-900">{{ $totalJamAll }} Jam</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CATATAN KAKI -->
    <div class="mt-4 text-[10px] italic space-y-0.5 text-slate-700">
        <p class="font-bold not-italic">Catatan:</p>
        <p>1. Asumsi Mata Pelajaran PA&PB dan PJOK diampu oleh Guru Mata Pelajaran, sehingga jadwal diatur tidak berbenturan waktu antar kelas.</p>
        <p>2. Kegiatan pembelajaran setiap hari Jum'at berakhir pukul 10.50 WIB, untuk semua kelas.</p>
        <p>3. Istirahat ke-2 untuk kegiatan makan siang & sholat disediakan waktu 45 menit.</p>
        <p>4. Kegiatan upacara kalau tidak cukup 35 menit maka sekolah bisa memulai lebih awal, sebelum pk. 07.00 WIB.</p>
        <p>5. Pembiasaan pagi bisa dilaksanakan sebelum pukul 07.00 WIB atau waktu/jadwal pelajaran dimulai pk. 07.15 WIB, dan selanjutnya jam akhir pelajaran menyesuaikan.</p>
    </div>

</body>
</html>