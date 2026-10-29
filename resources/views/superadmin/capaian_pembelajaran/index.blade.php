@extends('layouts.superadmin')

@section('title', 'Master Capaian Pembelajaran')

@section('content')

    Upload & Ekstrak Master Capaian Pembelajaran (CP)

    <!-- Flash Alert Message -->
    @if(session('success'))
        <div
            class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()"
                class="text-emerald-500 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div
            class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 font-semibold rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()"
                class="text-rose-500 hover:text-rose-800">&times;</button>
        </div>
    @endif

    <!-- Form Import (Berbasis Paste Teks Aman untuk Vercel & Lokal) -->
    <form action="{{ route('superadmin.capaian-pembelajaran.import-pdf') }}" method="POST" enctype="multipart/form-data"
        class="bg-slate-50 p-5 rounded-xl border border-slate-200 mb-6 space-y-4" id="uploadForm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Mata Pelajaran <span
                        class="text-rose-500">*</span></label>
                <select name="mapel_id" required
                    class="w-full text-sm rounded-xl border-slate-200 py-2 px-3 bg-white focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target Fase / Kelas</label>
                <select name="fase" required
                    class="w-full text-sm rounded-xl border-slate-200 py-2 px-3 bg-white text-slate-700 font-medium focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="AUTO" selected>✨ Deteksi Otomatis Seluruh Fase (A - D)</option>
                    <option value="A">Khusus Fase A (Kelas 1 - 2 SD)</option>
                    <option value="B">Khusus Fase B (Kelas 3 - 4 SD)</option>
                    <option value="C">Khusus Fase C (Kelas 5 - 6 SD)</option>
                    <option value="D">Khusus Fase D (Kelas 7 - 9 SMP)</option>
                </select>
            </div>
        </div>

        <!-- Kolom Teks CP (Utama & Paling Stabil di Server Online) -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tempel (Paste) Teks CP <span
                    class="text-rose-500">*</span></label>
            <textarea name="deskripsi" rows="5" required
                placeholder="Tempel teks ringkasan Capaian Pembelajaran di sini (Contoh: Al-Qur'an Hadis: Peserta didik mampu... Akidah: Peserta didik mampu...)..."
                class="w-full text-sm rounded-xl border-slate-200 p-3 bg-white focus:ring-emerald-500 focus:border-emerald-500"></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" id="submitBtn"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-sm transition flex items-center gap-2">
                <span id="btnIcon">📄</span>
                <span id="btnText">Simpan & Ekstrak Data CP</span>
            </button>
        </div>
    </form>

    <!-- Header & Filter Dropdown / Tab Per Fase -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <h2 class="text-lg font-bold text-slate-800">Daftar Capaian Pembelajaran Terkelompokkan</h2>

        <!-- Tombol Filter Cepat Per Fase -->
        <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-xl border border-slate-200 overflow-x-auto">
            <a href="{{ route('superadmin.capaian-pembelajaran.index', ['fase' => 'ALL']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($filterFase ?? 'ALL') == 'ALL' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                Semua Fase
            </a>
            @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $f)
                <a href="{{ route('superadmin.capaian-pembelajaran.index', ['fase' => $f]) }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($filterFase ?? '') == $f ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                    Fase {{ $f }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Tabel Tunggal Ber-Rowspan Sesuai Skema Layout -->
    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse border border-slate-200">
                <thead class="text-xs text-slate-700 uppercase bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 border border-slate-200 w-32 text-center">FASE</th>
                        <th class="px-4 py-3 border border-slate-200 w-48 text-center">MATA PELAJARAN</th>
                        <th class="px-4 py-3 border border-slate-200 w-52">ELEMEN</th>
                        <th class="px-4 py-3 border border-slate-200">DESKRIPSI CAPAIAN PEMBELAJARAN (CP)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($cpsByFase as $fase => $mapelGroups)
                        @php
                            $faseRowspan = 0;
                            foreach ($mapelGroups as $items) {
                                $faseRowspan += count($items);
                            }
                        @endphp

                        @foreach($mapelGroups as $mapelId => $items)
                            @php
                                $mapelRowspan = count($items);
                                $mapelNama = $items->first()->mapel->nama_mapel ?? '-';
                            @endphp

                            @foreach($items as $index => $cp)
                                <tr class="hover:bg-slate-50 transition">

                                    {{-- 1. KOLOM FASE (Merged Rowspan per Fase) --}}
                                    @if($loop->parent->first && $loop->first)
                                        <td rowspan="{{ $faseRowspan }}"
                                            class="px-4 py-4 border border-slate-200 text-center font-black bg-slate-900 text-white align-middle">
                                            <div class="flex flex-col items-center justify-center space-y-1">
                                                <span
                                                    class="bg-emerald-500 text-slate-950 text-xs px-2.5 py-1 rounded-md uppercase font-extrabold">
                                                    FASE {{ strtoupper($fase) }}
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-normal">
                                                    @if(strtoupper($fase) == 'A') (Kelas 1-2)
                                                    @elseif(strtoupper($fase) == 'B') (Kelas 3-4)
                                                    @elseif(strtoupper($fase) == 'C') (Kelas 5-6)
                                                    @elseif(strtoupper($fase) == 'D') (Kelas 7-9)
                                                    @else (Umum)
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                    @endif

                                    {{-- 2. KOLOM MATA PELAJARAN (Merged Rowspan per Mapel) --}}
                                    @if($loop->first)
                                        <td rowspan="{{ $mapelRowspan }}"
                                            class="px-4 py-3 border border-slate-200 font-bold text-slate-800 align-middle bg-slate-50 text-center">
                                            {{ $mapelNama }}
                                        </td>
                                    @endif

                                    {{-- 3. KOLOM ELEMEN --}}
                                    <td class="px-4 py-3 border border-slate-200 font-semibold text-slate-700 bg-white">
                                        <span
                                            class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs inline-block">
                                            {{ $cp->elemen }}
                                        </span>
                                    </td>

                                    {{-- 4. KOLOM DESKRIPSI CP --}}
                                    <td
                                        class="px-4 py-3 border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-white">
                                        {{ $cp->deskripsi_cp ?? $cp->deskripsi ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400 bg-slate-50">
                                Tidak ada data Capaian Pembelajaran untuk kriteria ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- Modal Animasi Pemindai AI / Processing Overlay -->
    <div id="aiScanningModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm hidden">
        <div
            class="bg-white border border-slate-200 p-8 rounded-2xl shadow-2xl max-w-md w-full mx-4 text-center relative overflow-hidden">
            <!-- Efek Laser Scanning Garis Berjalan -->
            <div
                class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent animate-pulse">
            </div>

            <div
                class="relative w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-emerald-50 rounded-full border border-emerald-200">
                <svg class="w-10 h-10 text-emerald-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <div class="absolute inset-0 rounded-full border-2 border-emerald-500/30 animate-ping"></div>
            </div>

            <h3 class="text-lg font-bold text-slate-900 mb-2">Memindai & Mengekstrak CP</h3>
            <p id="scanningStatusText" class="text-xs text-slate-500 font-medium leading-relaxed mb-6">
                AI sedang menganalisis struktur teks dan mengelompokkan elemen Kurikulum Merdeka secara presisi...
            </p>

            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mb-2">
                <div id="scanningProgressBar" class="bg-emerald-600 h-full w-0 transition-all duration-300 rounded-full">
                </div>
            </div>
            <span id="scanningProgressPercent" class="text-[11px] font-bold text-emerald-600">0%</span>
        </div>
    </div>

    <!-- Script Animasi Pemindai Profesional -->
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function (e) {
            const modal = document.getElementById('aiScanningModal');
            const progressBar = document.getElementById('scanningProgressBar');
            const progressPercent = document.getElementById('scanningProgressPercent');
            const statusText = document.getElementById('scanningStatusText');

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            // Tampilkan Modal Pemindai
            modal.classList.remove('hidden');

            // Simulasi Progress Bar Animasi Realistis
            let progress = 0;
            const statuses = [
                "Menghubungkan ke Mesin Analisis AI...",
                "Memilah Elemen dan Format Runtut...",
                "Menerapkan Aturan Kurikulum & Validasi...",
                "Menyimpan Hasil ke Database..."
            ];

            const interval = setInterval(function () {
                progress += Math.floor(Math.random() * 8) + 4;
                if (progress > 92) progress = 92; // Tahan di 92% sampai benar-benar redirect

                progressBar.style.width = progress + '%';
                progressPercent.innerText = progress + '%';

                if (progress > 75) {
                    statusText.innerText = statuses[3];
                } else if (progress > 50) {
                    statusText.innerText = statuses[2];
                } else if (progress > 25) {
                    statusText.innerText = statuses[1];
                } else {
                    statusText.innerText = statuses[0];
                }
            }, 250);
        });
    </script>


@endsection