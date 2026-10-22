@extends('layouts.guru')

@section('title', 'Nilai Formatif')
@section('page_title', 'Nilai Formatif')

@section('content')
<div class="space-y-6">

    <!-- Header & Tab Navigation -->
    <div class="flat-card p-6 bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Manajemen Nilai Formatif</h1>
                <p class="text-xs text-slate-500 mt-0.5">Input nilai siswa per Tujuan Pembelajaran (TP) atau lihat rekapitulasi nilai kelas</p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center p-1 bg-slate-100 rounded-lg border border-slate-200 self-start md:self-auto">
                <a href="{{ route('guru.nilai-formatif.index', array_merge(request()->query(), ['tab' => 'input'])) }}"
                   class="px-4 py-2 text-xs font-semibold rounded-md transition flex items-center gap-2 {{ request('tab', 'input') === 'input' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-pen-to-square"></i> Input Nilai
                </a>
                <a href="{{ route('guru.nilai-formatif.index', array_merge(request()->query(), ['tab' => 'rekap'])) }}"
                   class="px-4 py-2 text-xs font-semibold rounded-md transition flex items-center gap-2 {{ request('tab') === 'rekap' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-table-list"></i> Rekap Nilai Kelas
                </a>
            </div>
        </div>

        <!-- Form Filter -->
        <form method="GET" action="{{ route('guru.nilai-formatif.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="tab" value="{{ request('tab', 'input') }}">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelas *</label>
                <select name="kelas_id" required onchange="this.form.submit()" class="flat-input w-full p-2.5 text-xs rounded-lg border-slate-300">
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
                <select name="mapel_id" required onchange="this.form.submit()" class="flat-input w-full p-2.5 text-xs rounded-lg border-slate-300">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($listMapel as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('tab', 'input') === 'input')
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tujuan Pembelajaran (TP) *</label>
                <select name="tp_id" required class="flat-input w-full p-2.5 text-xs rounded-lg border-slate-300">
                    <option value="">-- Pilih TP --</option>
                    @forelse($listTp as $tp)
                        <option value="{{ $tp->id }}" {{ request('tp_id') == $tp->id ? 'selected' : '' }}>
                            {{ $tp->kode_tp ? '['.$tp->kode_tp.'] ' : '' }}{{ Str::limit($tp->deskripsi_tp ?? $tp->nama_tp, 50) }}
                        </option>
                    @empty
                        <option value="" disabled>-- Tidak Ada TP untuk Mapel ini --</option>
                    @endforelse
                </select>
            </div>
            @endif

            <div class="flex items-end {{ request('tab') === 'rekap' ? 'md:col-span-2' : '' }}">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i> 
                    {{ request('tab', 'input') === 'input' ? 'Tampilkan Lembar Input' : 'Tampilkan Rekap Nilai' }}
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 1: INPUT NILAI FORMATIF -->
    @if(request('tab', 'input') === 'input')
        @if(request()->filled(['kelas_id', 'mapel_id', 'tp_id']))
        <form action="{{ route('guru.nilai-formatif.store') }}" method="POST">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
            <input type="hidden" name="mapel_id" value="{{ request('mapel_id') }}">
            <input type="hidden" name="tp_id" value="{{ request('tp_id') }}">

            <div class="flat-card bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">
                        Input Nilai Siswa (Total: {{ $listSiswa->count() }} Siswa)
                    </span>
                    <span class="text-[11px] text-slate-500 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md font-medium">
                        Skala Nilai: 0.00 - 100.00
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-700 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5 w-32">NISN</th>
                                <th class="p-3.5">Nama Siswa</th>
                                <th class="p-3.5 w-16 text-center">L/P</th>
                                <th class="p-3.5 w-36 text-center">Nilai Formatif</th>
                                <th class="p-3.5">Catatan / Deskripsi Capaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($listSiswa as $index => $siswa)
                                @php
                                    $nilaiExisting = $nilaiMap[$siswa->id] ?? null;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-3.5 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="p-3.5 font-mono text-slate-600">{{ $siswa->nisn ?? '-' }}</td>
                                    <td class="p-3.5 font-semibold text-slate-800">{{ $siswa->nama_siswa ?? $siswa->nama_lengkap }}</td>
                                    <td class="p-3.5 text-center font-semibold text-slate-600">{{ $siswa->jenis_kelamin }}</td>
                                    <td class="p-3.5 text-center">
                                        <input type="number" 
                                               name="nilai[{{ $siswa->id }}]" 
                                               value="{{ old('nilai.'.$siswa->id, $nilaiExisting->nilai ?? '') }}" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100" 
                                               class="flat-input w-28 p-2 text-center text-xs font-bold rounded-md border-slate-300 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="p-3.5">
                                        <input type="text" 
                                               name="catatan[{{ $siswa->id }}]" 
                                               value="{{ old('catatan.'.$siswa->id, $nilaiExisting->catatan ?? '') }}" 
                                               placeholder="Catatan perkembangan siswa..." 
                                               class="flat-input w-full p-2 text-xs rounded-md border-slate-300">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        <i class="fa-solid fa-user-slash text-2xl mb-2 block text-slate-300"></i>
                                        Tidak ada data siswa ditemukan di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($listSiswa->count() > 0)
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai Formatif
                    </button>
                </div>
                @endif
            </div>
        </form>
        @else
        <div class="flat-card p-12 bg-white rounded-xl shadow-sm border border-slate-200 text-center text-slate-400">
            <i class="fa-solid fa-hand-pointer text-3xl mb-3 text-blue-500"></i>
            <h3 class="text-sm font-bold text-slate-700">Silakan Pilih Filter Terlebih Dahulu</h3>
            <p class="text-xs text-slate-400 mt-1">Pilih Kelas, Mata Pelajaran, dan Tujuan Pembelajaran untuk menampilkan lembar input nilai.</p>
        </div>
        @endif
    @endif

    <!-- TAB 2: REKAPITULASI NILAI KELAS -->
    @if(request('tab') === 'rekap')
        @if(request()->filled(['kelas_id', 'mapel_id']))
        <div class="flat-card bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">
                    Rekapitulasi Nilai Seluruh TP (Total: {{ $listSiswa->count() }} Siswa)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 border-collapse">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-700 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="p-3.5 w-12 text-center border-r border-slate-200">No</th>
                            <th class="p-3.5 w-32 border-r border-slate-200">NISN</th>
                            <th class="p-3.5 min-w-[180px] border-r border-slate-200">Nama Siswa</th>
                            
                            <!-- Header TP -->
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
                                    Tidak ada data siswa ditemukan di kelas ini.
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
    @endif

</div>
@endsection