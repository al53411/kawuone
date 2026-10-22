@extends('layouts.guru')

@section('title', 'Rekap Nilai Formatif')
@section('page_title', 'Rekap Nilai Formatif')

@section('content')
<div class="space-y-6">

    <!-- Card Filter Header -->
    <div class="flat-card p-6 bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Rekapitulasi Nilai Formatif</h1>
                <p class="text-xs text-slate-500 mt-0.5">Melihat hasil pencapaian nilai formatif siswa untuk seluruh Tujuan Pembelajaran (TP)</p>
            </div>
        </div>

        <!-- Form Filter -->
        <form method="GET" action="{{ route('guru.nilai-formatif.rekap') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelas *</label>
                <select name="kelas_id" required class="flat-input w-full p-2.5 text-xs rounded-lg border-slate-300">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($listKelas as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mata Pelajaran *</label>
                <select name="mapel_id" required class="flat-input w-full p-2.5 text-xs rounded-lg border-slate-300">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($listMapel as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-eye text-xs"></i> Tampilkan Rekap Nilai
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Hasil Rekap Nilai -->
    @if(request()->filled(['kelas_id', 'mapel_id']))
    <div class="flat-card bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700">
                Data Hasil Nilai Siswa (Total: {{ $listSiswa->count() }} Siswa)
            </span>
            <div class="flex items-center gap-2">
                <a href="{{ route('guru.nilai-formatif.index', ['kelas_id' => request('kelas_id'), 'mapel_id' => request('mapel_id')]) }}" 
                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i> Input / Edit Nilai
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 border-collapse">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-700 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="p-3.5 w-12 text-center border-r border-slate-200">No</th>
                        <th class="p-3.5 w-32 border-r border-slate-200">NISN</th>
                        <th class="p-3.5 min-w-[180px] border-r border-slate-200">Nama Siswa</th>
                        
                        <!-- Header Dinamis berdasarkan TP yang ada -->
                        @foreach($listTp as $tp)
                            <th class="p-3.5 text-center min-w-[100px] border-r border-slate-200" title="{{ $tp->deskripsi_tp }}">
                                {{ $tp->kode_tp ?? 'TP '.$loop->iteration }}
                            </th>
                        @endforeach
                        
                        <th class="p-3.5 text-center w-28 font-bold bg-blue-50 text-blue-800">Rata-Rata Formatif</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($listSiswa as $index => $siswa)
                        @php
                            $totalNilai = 0;
                            $jumlahTpTerisi = 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-3.5 text-center font-bold text-slate-400 border-r border-slate-100">{{ $index + 1 }}</td>
                            <td class="p-3.5 font-mono text-slate-600 border-r border-slate-100">{{ $siswa->nisn ?? '-' }}</td>
                            <td class="p-3.5 font-semibold text-slate-800 border-r border-slate-100">{{ $siswa->nama_siswa ?? $siswa->nama_lengkap }}</td>
                            
                            <!-- Nilai Per TP -->
                            @foreach($listTp as $tp)
                                @php
                                    $record = $matrixNilai[$siswa->id][$tp->id] ?? null;
                                    $nilai = $record ? $record->nilai : null;
                                    if($nilai !== null) {
                                        $totalNilai += $nilai;
                                        $jumlahTpTerisi++;
                                    }
                                @endphp
                                <td class="p-3.5 text-center border-r border-slate-100">
                                    @if($nilai !== null)
                                        <span class="font-bold {{ $nilai < 70 ? 'text-rose-600' : 'text-slate-700' }}">
                                            {{ number_format($nilai, 0) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 font-mono">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <!-- Rata-rata -->
                            @php
                                $rataRata = $jumlahTpTerisi > 0 ? ($totalNilai / $jumlahTpTerisi) : null;
                            @endphp
                            <td class="p-3.5 text-center font-extrabold bg-blue-50/50 text-blue-700">
                                {{ $rataRata !== null ? number_format($rataRata, 1) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($listTp) + 4 }}" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-user-slash text-2xl mb-2 block text-slate-300"></i>
                                Tidak ada siswa terdaftar di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="flat-card p-12 bg-white rounded-xl shadow-sm border border-slate-200 text-center text-slate-400">
        <i class="fa-solid fa-table-list text-3xl mb-3 text-blue-500"></i>
        <h3 class="text-sm font-bold text-slate-700">Silakan Pilih Kelas & Mata Pelajaran</h3>
        <p class="text-xs text-slate-400 mt-1">Pilih filter di atas untuk menampilkan hasil rekapitulasi nilai formatif siswa.</p>
    </div>
    @endif

</div>
@endsection