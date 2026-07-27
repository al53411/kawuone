@extends('layouts.admin')

@section('title', 'Absensi Siswa')
@section('page_title', 'Absensi')

@section('content')

    <div class="max-w-5xl mx-auto mb-6 px-4 no-print">
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Filter Rekap Absensi Mapel</h2>
            <form action="{{ route('admin.absensi.cetak-mapel') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500" required>
                        <option value="">-- Kelas --</option>
                        @foreach($daftar_kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Mata Pelajaran</label>
                    <input type="text" name="mapel" value="{{ request('mapel') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500" placeholder="Contoh: PJOK" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Bulan</label>
                    <input type="month" name="bulan" value="{{ request('bulan', date('Y-m')) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded text-sm transition-all duration-150">
                        Tampilkan
                    </button>
                    @if($kelas_aktif && $mapel)
                        <button type="button" onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded text-sm flex items-center gap-1.5 transition-all duration-150">
                            Cetak / PDF
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($kelas_aktif && $mapel)
    <div class="max-w-5xl mx-auto bg-white p-10 rounded-lg shadow-md print-area">
        <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
            <div class="w-20 h-20 bg-gray-200 flex items-center justify-center font-bold text-xs border border-gray-400">LOGO DISDIK</div>
            <div class="text-center flex-1 px-4">
                <h1 class="text-xl font-bold uppercase tracking-wide text-gray-900">Pemerintah Kabupaten Ngawi</h1>
                <h2 class="text-lg font-semibold uppercase text-gray-800">Dinas Pendidikan dan Kebudayaan</h2>
                <h3 class="text-2xl font-bold uppercase text-black">SDN KAWU 1</h3>
                <p class="text-xs text-gray-600 italic">Alamat: Jl. Raya Kawu No. 1, Kec. Kedunggalar, Kabupaten Ngawi</p>
            </div>
            <div class="w-20 h-20 bg-gray-200 flex items-center justify-center font-bold text-xs border border-gray-400">LOGO SEKOLAH</div>
        </div>

        <div class="text-center mb-6">
            <h4 class="text-lg font-bold uppercase underline decoration-1">Laporan Presensi Siswa Mata Pelajaran</h4>
            <p class="text-sm font-medium text-gray-700 mt-1">Bulan: {{ Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-4 font-semibold text-gray-800">
            <div>
                <p>Mata Pelajaran &nbsp;: <span class="font-bold underline">{{ $mapel }}</span></p>
                <p>Kelas &emsp;&emsp;&emsp;&emsp;&nbsp;: {{ $kelas_aktif->nama_kelas }}</p>
            </div>
            <div class="text-right">
                <p>Wali Kelas : {{ $kelas_aktif->wali_kelas ?? '-' }}</p>
                <p>Sekolah &nbsp;&nbsp;&nbsp;&nbsp;: {{ $profilSekolah->nama_sekolah ?? 'SDN KAWU 1' }}</p>
            </div>
        </div>

        <table class="w-full border-collapse border border-black text-xs text-center">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-black px-2 py-3 w-10" rowspan="2">No</th>
                    <th class="border border-black px-3 py-3 text-left min-w-[150px]" rowspan="2">Nama Siswa</th>
                    <th class="border border-black px-2 py-1" colspan="{{ max(1, count($tanggal_list)) }}">Tanggal Pertemuan</th>
                    <th class="border border-black px-2 py-1 w-24" colspan="4">Keterangan</th>
                </tr>
                <tr class="bg-gray-100">
                    @forelse($tanggal_list as $tgl)
                        <th class="border border-black px-1 py-2 font-mono text-[10px] whitespace-nowrap">
                            {{ Carbon\Carbon::parse($tgl)->format('d/m') }}
                        </th>
                    @empty
                        <th class="border border-black px-1 py-2 text-gray-400 italic">Belum ada pertemuan</th>
                    @endforelse
                    <th class="border border-black px-1 py-1 bg-emerald-50 text-emerald-800 font-bold">H</th>
                    <th class="border border-black px-1 py-1 bg-amber-50 text-amber-800 font-bold">S</th>
                    <th class="border border-black px-1 py-1 bg-blue-50 text-blue-800 font-bold">I</th>
                    <th class="border border-black px-1 py-1 bg-red-50 text-red-800 font-bold">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswas as $index => $siswa)
                    @php
                        $hadir = 0; $sakit = 0; $izin = 0; $alfa = 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors duration-100">
                        <td class="border border-black px-2 py-2">{{ $index + 1 }}</td>
                        <td class="border border-black px-3 py-2 text-left font-semibold">{{ $siswa->nama_lengkap }}</td>
                        
                        @forelse($tanggal_list as $tgl)
                            @php
                                $status = $rekap_absen[$siswa->id][$tgl] ?? '-';
                                if($status == 'Hadir') $hadir++;
                                if($status == 'Sakit') $sakit++;
                                if($status == 'Izin') $izin++;
                                if($status == 'Alfa') $alfa++;
                            @endphp
                            <td class="border border-black px-1 py-2 font-bold">
                                @if($status == 'Hadir') <span class="text-emerald-700">H</span>
                                @elseif($status == 'Sakit') <span class="text-amber-600">S</span>
                                @elseif($status == 'Izin') <span class="text-blue-600">I</span>
                                @elseif($status == 'Alfa') <span class="text-red-600">A</span>
                                @else -
                                @endif
                            </td>
                        @empty
                            <td class="border border-black px-1 py-2 text-gray-300">-</td>
                        @endforelse

                        <td class="border border-black px-1 py-2 font-bold bg-emerald-50 text-emerald-800">{{ $hadir }}</td>
                        <td class="border border-black px-1 py-2 font-bold bg-amber-50 text-amber-800">{{ $sakit }}</td>
                        <td class="border border-black px-1 py-2 font-bold bg-blue-50 text-blue-800">{{ $izin }}</td>
                        <td class="border border-black px-1 py-2 font-bold bg-red-50 text-red-800">{{ $alfa }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-12 grid grid-cols-2 text-sm text-center">
            <div>
                <p>Mengetahui,</p>
                <p class="font-semibold">Kepala Sekolah {{ $profilSekolah->nama_sekolah ?? 'SDN KAWU 1' }}</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $profilSekolah->nama_kepala_sekolah ?? '............................................' }}</p>
                <p class="text-xs text-gray-600">NIP. {{ $profilSekolah->npsn ?? '............................................' }}</p>
            </div>
            <div>
                <p>Kedunggalar, {{ Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
                <p class="font-semibold">Guru Mata Pelajaran {{ $mapel }}</p>
                <div class="h-24"></div>
                <p class="font-bold underline">............................................</p>
                <p class="text-xs text-gray-600">NIP. ............................................</p>
            </div>
        </div>
    </div>
    @else
    <div class="max-w-5xl mx-auto text-center py-20 text-gray-500 no-print">
        <p class="text-lg">Silakan pilih Filter Kelas dan Mata Pelajaran di atas terlebih dahulu.</p>
    </div>
    @endif
@endsection