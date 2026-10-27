@extends('layouts.guru')

@section('title', $profilSekolah->nama_sekolah ?? 'SDN Kawu 1')

@section('content')
    <div class="w-full bg-white text-slate-800 p-4 sm:p-6">

        <!-- Header & Filter Cetak -->
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Jurnal Pembelajaran Guru</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catatan aktivitas KBM harian yang terintegrasi dengan
                    validasi Kepala Sekolah.</p>
            </div>

            <form action="{{ route('guru.jurnal.cetak') }}" method="GET"
                class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                <div>
                    <select name="bulan"
                        class="text-xs rounded-xl border-slate-200 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Semua Bulan --</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <select name="tahun"
                        class="text-xs rounded-xl border-slate-200 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Semua Tahun --</option>
                        @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm whitespace-nowrap">
                    <span>📄</span> Cetak Rekap Word
                </button>
            </form>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div
                class="mt-4 p-3.5 sm:p-4 text-xs sm:text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Alert Error -->
        @if (session('error'))
            <div
                class="mt-4 p-3.5 sm:p-4 text-xs sm:text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">🚫</span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Alert Validation Errors -->
        @if ($errors->any())
            <div class="mt-4 p-3.5 sm:p-4 text-xs sm:text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <span>⚠️</span> Terjadi kesalahan input:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Input Jurnal -->
        <div class="mt-4 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span>📝</span> Input Jurnal Mengajar Baru
            </h2>

            <form action="{{ route('guru.jurnal.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Row 1: Tanggal, Kelas, Mapel, Jam Ke -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mengajar</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kelas</label>
                        <select name="kelas_id" id="kelas_id" required
                            class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelases as $kelas)
                                <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Pelajaran</label>
                    <select name="mapel" id="mapel" required disabled class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 bg-slate-50">
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                        @foreach ($mapels as $mapel)
                            @php
                                $kelasIdsMapel = method_exists($mapel, 'kelases') ? $mapel->kelases->pluck('id')->toArray() : [];
                                $namaKelasList = method_exists($mapel, 'kelases') ? $mapel->kelases->pluck('nama_kelas')->implode(', ') : '';
                                $infoKelas = $namaKelasList ? " (Kelas: {$namaKelasList})" : " (Semua Kelas)";
                            @endphp
                            <option value="{{ $mapel->nama_mapel }}" 
                                    data-id="{{ $mapel->id }}" 
                                    data-kelas-ids="{{ json_encode($kelasIdsMapel) }}"
                                    {{ old('mapel') == $mapel->nama_mapel ? 'selected' : '' }}>
                                [{{ $mapel->kode_mapel ?? 'MPL-XXX' }}] {{ $mapel->nama_mapel }}{{ $infoKelas }}
                            </option>
                        @endforeach
                    </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Ke-</label>
                        <input type="text" name="jam_ke" value="{{ old('jam_ke') }}"
                            placeholder="Contoh: 1-2 (07.00 - 08.10)" required
                            class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                    </div>
                </div>

                <!-- Row 2: Materi / TP & Kegiatan Pembelajaran -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Materi / Tujuan Pembelajaran (TP)</label>
                        <!-- Dropdown Pilih TP -->
                        <select id="select_tp" name="select_tp"
                            class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 mb-2 bg-slate-50">
                            <option value="">-- Pilih Kelas & Mapel Terlebih Dahulu --</option>
                            @if (isset($tujuanPembelajarans) && count($tujuanPembelajarans) > 0)
                                @foreach ($tujuanPembelajarans as $tp)
                                    <option value="{{ $tp->deskripsi_tp }}" data-kelas="{{ $tp->kelas_id ?? '' }}"
                                        data-mapel-id="{{ $tp->mapel_id ?? '' }}"
                                        data-mapel-nama="{{ $tp->mapel->nama_mapel ?? '' }}">
                                        {{ $tp->kode_tp ? '[' . $tp->kode_tp . '] ' : '' }}{{ \Illuminate\Support\Str::limit($tp->deskripsi_tp, 70) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        <textarea name="materi" id="materi" rows="3"
                            placeholder="Atau ketik materi pembelajaran secara manual..." required
                            class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 p-3">{{ old('materi') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kegiatan Pembelajaran</label>
                        <textarea name="kegiatan" rows="4" placeholder="Deskripsi aktivitas/metode pembelajaran..." required
                            class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 p-3">{{ old('kegiatan') }}</textarea>
                    </div>
                </div>

                <!-- Row 3: Keterangan Opsional -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        placeholder="Contoh: Selesai / Dilanjutkan minggu depan"
                        class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        <span>💾</span> Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>

        <!-- Riwayat Jurnal Pembelajaran -->
        <div class="mt-6 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span>📖</span> Riwayat Jurnal Pembelajaran
            </h2>

            <!-- Tampilan Mobile (Card List) -->
            <div class="block md:hidden space-y-3.5">
                @forelse ($jurnals as $jurnal)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-200/80 pb-2.5">
                            <div>
                                <div class="font-bold text-sm text-slate-800">{{ $jurnal->hari }},
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-indigo-600 font-medium">Jam Ke: {{ $jurnal->jam_ke }}</div>
                            </div>
                            <div>
                                @if ($jurnal->status_validasi === 'Disetujui')
                                    <span
                                        class="px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-100 text-emerald-800 rounded-full inline-block">Disetujui</span>
                                @elseif ($jurnal->status_validasi === 'Ditolak')
                                    <span
                                        class="px-2.5 py-0.5 text-[11px] font-semibold bg-rose-100 text-rose-800 rounded-full inline-block">Ditolak</span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 text-[11px] font-semibold bg-amber-100 text-amber-800 rounded-full inline-block">Pending</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded border border-blue-100">
                                {{ $jurnal->kelas->nama_kelas ?? 'Kelas -' }}
                            </span>
                            <span class="font-semibold text-xs text-slate-800">{{ $jurnal->mapel }}</span>
                        </div>

                        <div class="text-xs space-y-1.5 text-slate-700">
                            <div>
                                <span class="font-semibold text-slate-500 block">Materi / TP:</span>
                                <p class="text-slate-800">{{ $jurnal->materi }}</p>
                            </div>
                            <div>
                                <span class="font-semibold text-slate-500 block">Kegiatan:</span>
                                <p class="text-slate-600">{{ $jurnal->kegiatan }}</p>
                            </div>
                            @if ($jurnal->keterangan)
                                <div class="italic text-slate-500 pt-1">
                                    Ket: {{ $jurnal->keterangan }}
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 flex justify-end items-center">
                            @if ($jurnal->status_validasi !== 'Disetujui')
                                <form action="{{ route('guru.jurnal.destroy', $jurnal->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 rounded-lg transition font-medium flex items-center gap-1 border border-rose-200">
                                        <span>🗑️</span> Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-400 text-xs font-medium flex items-center gap-1">🔒 Terkunci</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                        Belum ada riwayat jurnal mengajar.
                    </div>
                @endforelse
            </div>

            <!-- Tampilan Desktop (Table) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Hari, Tanggal & Jam</th>
                            <th class="px-4 py-3">Kelas & Mapel</th>
                            <th class="px-4 py-3">Materi / TP</th>
                            <th class="px-4 py-3">Kegiatan</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-center">Status Validasi</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($jurnals as $jurnal)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-4 font-medium text-slate-900 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $jurnal->hari }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-xs text-indigo-600 font-medium">Jam: {{ $jurnal->jam_ke }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded border border-blue-100 block w-max mb-1">
                                        {{ $jurnal->kelas->nama_kelas ?? 'Kelas -' }}
                                    </span>
                                    <span class="font-semibold text-slate-800">{{ $jurnal->mapel }}</span>
                                </td>
                                <td class="px-4 py-4 max-w-xs">
                                    <p class="line-clamp-2 text-slate-800 font-medium">{{ $jurnal->materi }}</p>
                                </td>
                                <td class="px-4 py-4 max-w-xs">
                                    <p class="line-clamp-2 text-slate-600">{{ $jurnal->kegiatan }}</p>
                                </td>
                                <td class="px-4 py-4 italic text-slate-500 text-xs whitespace-nowrap">
                                    {{ $jurnal->keterangan ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if ($jurnal->status_validasi === 'Disetujui')
                                        <span
                                            class="px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">Disetujui</span>
                                    @elseif ($jurnal->status_validasi === 'Ditolak')
                                        <span
                                            class="px-3 py-1 text-xs font-semibold bg-rose-100 text-rose-800 rounded-full">Ditolak</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if ($jurnal->status_validasi !== 'Disetujui')
                                        <form action="{{ route('guru.jurnal.destroy', $jurnal->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                title="Hapus Jurnal">
                                                🗑️
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">🔒 Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada riwayat jurnal mengajar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Script Pengendali Filter TP & Auto Insert ke Textarea -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectKelas = document.getElementById('kelas_id');
            const selectMapel = document.getElementById('mapel');
            const selectTp = document.getElementById('select_tp');
            const materiTextarea = document.getElementById('materi');

            let tpInfo = document.getElementById('tp_info_text');
            if (!tpInfo && selectTp) {
                tpInfo = document.createElement('p');
                tpInfo.id = 'tp_info_text';
                tpInfo.className = 'text-[11px] mt-1 mb-2 font-medium';
                selectTp.parentNode.insertBefore(tpInfo, selectTp.nextSibling);
            }

            // Fungsi Filter Mata Pelajaran Berdasarkan Kelas yang Dipilih
            function filterMapelByKelas() {
                if (!selectKelas || !selectMapel) return;

                const selectedKelasId = String(selectKelas.value || '').trim();
                const options = selectMapel.querySelectorAll('option');

                if (!selectedKelasId) {
                    // Jika Kelas belum dipilih: Nonaktifkan Mapel & sembunyikan semua opsi di dalamnya
                    selectMapel.disabled = true;
                    selectMapel.value = '';
                    selectMapel.classList.add('bg-slate-50');
                    selectMapel.options[0].textContent = '-- Pilih Kelas Terlebih Dahulu --';

                    options.forEach((option, index) => {
                        if (index > 0) option.style.display = 'none';
                    });

                    filterTpOptions();
                    return;
                }

                // Jika Kelas sudah dipilih: Aktifkan kembali Mapel
                selectMapel.disabled = false;
                selectMapel.classList.remove('bg-slate-50');
                selectMapel.options[0].textContent = '-- Pilih Mata Pelajaran --';

                let countMapelVisible = 0;

                options.forEach((option, index) => {
                    if (index === 0) return; // Lewati opsi default

                    const kelasIdsAttr = option.getAttribute('data-kelas-ids');
                    if (!kelasIdsAttr) {
                        option.style.display = '';
                        countMapelVisible++;
                        return;
                    }

                    try {
                        const kelasIds = JSON.parse(kelasIdsAttr);
                        // Tampilkan jika mapel berlaku untuk semua kelas atau mencakup kelas yang dipilih
                        if (kelasIds.length === 0 || kelasIds.includes(parseInt(selectedKelasId)) || kelasIds.includes(selectedKelasId)) {
                            option.style.display = '';
                            countMapelVisible++;
                        } else {
                            option.style.display = 'none';
                        }
                    } catch (e) {
                        option.style.display = '';
                        countMapelVisible++;
                    }
                });

                if (countMapelVisible === 0) {
                    selectMapel.options[0].textContent = '-- Tidak Ada Mapel untuk Kelas Ini --';
                }

                // Reset pilihan mapel jika mapel yang tadinya dipilih menjadi tersembunyi
                if (selectMapel.selectedIndex > 0 && selectMapel.options[selectMapel.selectedIndex].style.display === 'none') {
                    selectMapel.value = '';
                }

                filterTpOptions();
            }

            function filterTpOptions() {
                if (!selectKelas || !selectMapel || !selectTp) return;

                const selectedKelasId = String(selectKelas.value || '').trim();
                const selectedMapelNama = String(selectMapel.value || '').trim();
                
                const optMapel = selectMapel.options[selectMapel.selectedIndex];
                const selectedMapelId = optMapel ? String(optMapel.getAttribute('data-id') || '').trim() : '';

                if (!selectedKelasId || (!selectedMapelNama && !selectedMapelId)) {
                    selectTp.disabled = true;
                    selectTp.options[0].textContent = '-- Pilih Kelas & Mapel Terlebih Dahulu --';
                    selectTp.value = '';

                    if (tpInfo) {
                        tpInfo.className = 'text-[11px] mt-1 mb-2 font-medium text-amber-600';
                        tpInfo.innerHTML = '⚠️ Silakan pilih Kelas dan Mata Pelajaran terlebih dahulu.';
                    }

                    Array.from(selectTp.options).forEach((opt, idx) => {
                        if (idx > 0) opt.style.display = 'none';
                    });
                    return;
                }

                selectTp.disabled = false;
                selectTp.options[0].textContent = '-- Pilih dari Master TP (Opsional) --';

                let countVisible = 0;

                Array.from(selectTp.options).forEach((option, index) => {
                    if (index === 0) return;

                    const tpKelasId = String(option.getAttribute('data-kelas') || '').trim();
                    const tpMapelId = String(option.getAttribute('data-mapel-id') || '').trim();
                    const tpMapelNama = String(option.getAttribute('data-mapel-nama') || '').trim();

                    const matchKelas = (!tpKelasId || tpKelasId === '0' || tpKelasId === selectedKelasId);
                    const matchMapel = (selectedMapelId && tpMapelId && tpMapelId === selectedMapelId) ||
                                    (selectedMapelNama && tpMapelNama && selectedMapelNama.toLowerCase().includes(tpMapelNama.toLowerCase()));

                    if (matchKelas && matchMapel) {
                        option.style.display = '';
                        countVisible++;
                    } else {
                        option.style.display = 'none';
                    }
                });

                if (countVisible === 0) {
                    selectTp.options[0].textContent = '-- Tidak Ada TP untuk Kombinasi Ini --';
                    if (tpInfo) {
                        tpInfo.className = 'text-[11px] mt-1 mb-2 font-medium text-rose-600';
                        tpInfo.innerHTML = `❌ Data TP belum diinput untuk Kelas dan Mapel ini.`;
                    }
                } else {
                    if (tpInfo) {
                        tpInfo.className = 'text-[11px] mt-1 mb-2 font-medium text-emerald-600';
                        tpInfo.innerHTML = `✅ Ditemukan ${countVisible} Tujuan Pembelajaran yang sesuai.`;
                    }
                }

                if (selectTp.selectedIndex > 0 && selectTp.options[selectTp.selectedIndex].style.display === 'none') {
                    selectTp.value = '';
                }
            }

            // Event Listeners
            if (selectKelas) {
                selectKelas.addEventListener('change', filterMapelByKelas);
            }
            if (selectMapel) {
                selectMapel.addEventListener('change', filterTpOptions);
            }

            // Jalankan saat halaman pertama kali dimuat
            filterMapelByKelas();

            selectTp.addEventListener('change', function () {
                if (this.value) {
                    if (materiTextarea.value.trim() !== '') {
                        materiTextarea.value += "\n" + this.value;
                    } else {
                        materiTextarea.value = this.value;
                    }
                }
            });
        });
    </script>
    </div>
@endsection