@extends('layouts.guru')

@section('title', 'Rekap Absensi Mata Pelajaran')

@section('content')
    <div class="w-full bg-white text-slate-800 p-4 sm:p-6">

        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Rekap Absensi Mata Pelajaran</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Laporan rekapitulasi kehadiran siswa per mata pelajaran
                    bulanan.</p>
            </div>
            @if($kelasId && $mapelId)
                <a href="{{ route('guru.absensi-mapel.cetak', ['kelas_id' => $kelasId, 'mapel_id' => $mapelId, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                    target="_blank"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <span>🖨️</span> Cetak / PDF
                </a>
            @endif
        </div>

        <!-- Filter Form -->
        <form action="{{ route('guru.absensi-mapel.rekap') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 items-end mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Kelas</label>
                <select name="kelas_id" required class="w-full text-sm rounded-xl border-slate-200 py-2 px-3">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Pelajaran</label>
                <select name="mapel_id" required class="w-full text-sm rounded-xl border-slate-200 py-2 px-3">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ $mapelId == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Bulan</label>
                <select name="bulan" class="w-full text-sm rounded-xl border-slate-200 py-2 px-3">
                    @php
                        $bulanList = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ];
                    @endphp
                    @foreach($bulanList as $key => $namaBulan)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $namaBulan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun</label>
                <select name="tahun" class="w-full text-sm rounded-xl border-slate-200 py-2 px-3">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <button type="submit"
                    class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    🔍 Filter Rekap
                </button>
            </div>
        </form>

        @if(isset($siswaList) && count($siswaList) > 0)
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3 text-center">Hadir (H)</th>
                            <th class="px-4 py-3 text-center">Izin (I)</th>
                            <th class="px-4 py-3 text-center">Sakit (S)</th>
                            <th class="px-4 py-3 text-center">Alpa (A)</th>
                            <th class="px-4 py-3 text-center">Total Pertemuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($siswaList as $index => $siswa)
                            @php
                                $rekap = $rekapData[$siswa->id] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'total' => 0];
                                $namaSiswa = $siswa->nama_lengkap ?? $siswa->nama ?? $siswa->nama_siswa ?? '-';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-center text-slate-400 font-semibold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $namaSiswa }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-emerald-600">{{ $rekap['hadir'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-600">{{ $rekap['izin'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-amber-600">{{ $rekap['sakit'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-rose-600">{{ $rekap['alpa'] }}</td>
                                <td class="px-4 py-3 text-center font-bold text-slate-700">{{ $rekap['total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center border border-dashed border-slate-200 rounded-xl text-slate-500">
                <p>Silakan pilih Kelas dan Mata Pelajaran terlebih dahulu untuk melihat rekapitulasi.</p>
            </div>
        @endif

    </div>
@endsection