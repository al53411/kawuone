<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi {{ $kelas->nama_kelas ?? 'Kelas' }} - {{ $bulan }} {{ $tahun }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        table, th, td {
            border: 1px solid #334155 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans p-4 print:p-0 print:bg-white">

    <!-- Tombol Cetak / Download PDF (Sembunyi saat dicetak) -->
    <div class="max-w-[297mm] mx-auto mb-4 flex justify-between items-center no-print">
        <a href="{{ route('guru.absensi.rekap', ['kelas_id' => $kelasId, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
           class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition">
            ⬅️ Kembali
        </a>
        <button onclick="window.print()" 
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
            🖨️ Cetak / Simpan ke PDF
        </button>
    </div>

    <!-- Area Dokumen Cetak (Ukuran A4 Landscape) -->
    <div class="max-w-[297mm] mx-auto bg-white p-6 rounded-2xl shadow-sm border border-slate-200 print:border-none print:shadow-none print:p-0">
        
        <!-- 1. Kop Surat Sekolah -->
        <div class="text-center border-b-2 border-slate-900 pb-3 mb-4">
            <h2 class="text-xl font-bold uppercase tracking-wider text-slate-900">
                {{ $profilSekolah->nama_sekolah ?? 'SD NEGERI KAWU 1' }}
            </h2>
            <p class="text-xs text-slate-600">
                {{ $profilSekolah->alamat ?? 'Kecamatan Kedunggalar, Kabupaten Ngawi' }}
            </p>
            <h3 class="text-base font-bold uppercase mt-2 tracking-wide text-slate-800">
                REKAPITULASI PRESENSI SISWA HARIAN
            </h3>
        </div>

        <!-- 2. Informasi Kelas & Periode -->
        <div class="flex justify-between items-center text-xs font-semibold text-slate-700 mb-3">
            <div>
                <p>KELAS : <span class="font-bold text-slate-900">{{ strtoupper($kelas->nama_kelas ?? '-') }}</span></p>
                <p>SEMESTER : <span class="font-bold text-slate-900">{{ $semester ?? 'Ganjil/Genap' }}</span></p>
            </div>
            <div class="text-right">
                <p>BULAN : <span class="font-bold text-slate-900">{{ strtoupper(DateTime::createFromFormat('!m', (int)$bulan)->format('F')) }} {{ $tahun }}</span></p>
                <p>TAHUN AJARAN : <span class="font-bold text-slate-900">{{ $tahunAjaran ?? $tahun }}</span></p>
            </div>
        </div>

        <!-- 3. Tabel Rekap Bulanan (Tanggal 1 s/d Jumlah Hari Bulan Tersebut) -->
        @php
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-[10px] text-center border-collapse">
                <thead class="bg-slate-100 font-bold uppercase text-slate-800">
                    <tr>
                        <th rowspan="2" class="w-6 py-2">No</th>
                        <th rowspan="2" class="w-20">NISN</th>
                        <th rowspan="2" class="text-left px-2 w-48">Nama Siswa</th>
                        <th colspan="{{ $jumlahHari }}" class="py-1">Tanggal Bulan {{ DateTime::createFromFormat('!m', (int)$bulan)->format('F') }}</th>
                        <th colspan="4" class="w-20">Total</th>
                    </tr>
                    <tr>
                        @for($d = 1; $d <= $jumlahHari; $d++)
                            <th class="w-5 py-1">{{ $d }}</th>
                        @endfor
                        <th class="w-5 bg-emerald-100 text-emerald-900">H</th>
                        <th class="w-5 bg-blue-100 text-blue-900">I</th>
                        <th class="w-5 bg-amber-100 text-amber-900">S</th>
                        <th class="w-5 bg-rose-100 text-rose-900">A</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse($siswaList as $index => $siswa)
                        @php
                            $stat = $rekapData[$siswa->id] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                            $detailHarian = $matrixHarian[$siswa->id] ?? [];
                            $namaSiswa = $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-1.5 font-medium">{{ $index + 1 }}</td>
                            <td class="font-mono text-[9px]">{{ $siswa->nisn ?? '-' }}</td>
                            <td class="text-left px-2 font-bold text-slate-800 truncate max-w-[180px]">{{ $namaSiswa }}</td>
                            
                            <!-- Status Per Tanggal (1 - 31) -->
                            @for($d = 1; $d <= $jumlahHari; $d++)
                                @php
                                    $tglKey = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                    $statusKode = $detailHarian[$tglKey] ?? '.';
                                    
                                    // Pewarnaan kode status singkat (H, I, S, A)
                                    $colorClass = '';
                                    if ($statusKode === 'H') $colorClass = 'text-emerald-700 font-bold';
                                    elseif ($statusKode === 'I') $colorClass = 'bg-blue-100 text-blue-800 font-bold';
                                    elseif ($statusKode === 'S') $colorClass = 'bg-amber-100 text-amber-800 font-bold';
                                    elseif ($statusKode === 'A') $colorClass = 'bg-rose-200 text-rose-900 font-extrabold';
                                @endphp
                                <td class="py-1 {{ $colorClass }}">{{ $statusKode != '.' ? $statusKode : '' }}</td>
                            @endfor

                            <!-- Total Ringkasan Bulan Ini -->
                            <td class="font-bold text-emerald-800 bg-emerald-50/50">{{ $stat['hadir'] }}</td>
                            <td class="font-bold text-blue-800 bg-blue-50/50">{{ $stat['izin'] }}</td>
                            <td class="font-bold text-amber-800 bg-amber-50/50">{{ $stat['sakit'] }}</td>
                            <td class="font-bold text-rose-800 bg-rose-50/50">{{ $stat['alpa'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $jumlahHari + 8 }}" class="py-4 text-slate-500 italic">
                                Data siswa atau absensi tidak ditemukan untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Keterangan Simbol -->
        <div class="mt-2 text-[9px] text-slate-600 flex gap-4 font-semibold">
            <span>Keterangan Singkatan:</span>
            <span><strong class="text-emerald-700">H</strong> = Hadir</span>
            <span><strong class="text-blue-700">I</strong> = Izin</span>
            <span><strong class="text-amber-700">S</strong> = Sakit</span>
            <span><strong class="text-rose-700">A</strong> = Alpa / Tanpa Keterangan</span>
        </div>

        <!-- 4. Kolom Tanda Tangan Guru & Kepala Sekolah -->
        <div class="mt-8 grid grid-cols-2 text-center text-xs text-slate-800 font-medium break-inside-avoid">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold">Kepala Sekolah {{ $profilSekolah->nama_sekolah ?? '' }}</p>
                <div class="h-16"></div> <!-- Space Tanda Tangan -->
                <p class="font-bold underline uppercase">{{ $kepalaSekolah->nama ?? '.....................................' }}</p>
                <p class="text-[10px] text-slate-600">NIP. {{ $kepalaSekolah->nip ?? '.....................................' }}</p>
            </div>
            <div>
                <p>Kedunggalar, {{ date('t') }} {{ DateTime::createFromFormat('!m', (int)$bulan)->format('F') }} {{ $tahun }}</p>
                <p class="font-bold">Guru Kelas / Mata Pelajaran</p>
                <div class="h-16"></div> <!-- Space Tanda Tangan -->
                <p class="font-bold underline uppercase">{{ Auth::user()->name ?? $guru->nama ?? '.....................................' }}</p>
                <p class="text-[10px] text-slate-600">NIP. {{ Auth::user()->nip ?? $guru->nip ?? '.....................................' }}</p>
            </div>
        </div>

    </div>

</body>
</html>