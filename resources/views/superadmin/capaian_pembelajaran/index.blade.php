@extends('layouts.superadmin')

@section('title', 'Master Capaian Pembelajaran')

@section('content')
    <div class="w-full bg-white p-6 shadow-sm border border-slate-200 rounded-xl mb-6">
        <h1 class="text-xl font-bold text-slate-800 mb-4">Upload & Ekstrak Master Capaian Pembelajaran (CP)</h1>

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

        <!-- Form Import Mengarah ke Preview Terlebih Dahulu -->
        <!-- Form Import (Mendukung File PDF/Word & Teks Manual) -->
        <form action="{{ route('superadmin.capaian-pembelajaran.preview') }}" method="POST" enctype="multipart/form-data"
            class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4" id="uploadForm">
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

            <!-- Input File PDF / Word -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Unggah Berkas (PDF / Word .docx)</label>
                <input type="file" name="file_berkas" accept=".pdf,.doc,.docx"
                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 bg-white border border-slate-200 rounded-xl">
                <p class="text-[11px] text-slate-400 mt-1">Opsional jika ingin membaca langsung dari dokumen fisik/file
                    pemerintah.</p>
            </div>

            <!-- Kolom Teks CP (Alternatif Utama jika File Kosong) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Atau Tempel (Paste) Teks CP</label>
                <textarea name="deskripsi" rows="4"
                    placeholder="Atau tempel teks ringkasan Capaian Pembelajaran di sini jika tidak mengunggah file..."
                    class="w-full text-sm rounded-xl border-slate-200 p-3 bg-white focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" id="submitBtn"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-sm transition flex items-center gap-2">
                    <span id="btnIcon">📄</span>
                    <span id="btnText">Pratinjau Hasil Ekstrak CP</span>
                </button>
            </div>
        </form>

        <!-- Tabel Tunggal Ber-Rowspan Sesuai Skema Layout -->
        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse border border-slate-200">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-100 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 border border-slate-200 w-32 text-center">FASE</th>
                            <th class="px-4 py-3 border border-slate-200 w-48 text-center">MATA PELAJARAN</th>
                            <th class="px-4 py-3 border border-slate-200 w-52">ELEMEN</th>
                            <th class="px-4 py-3 border border-slate-200">DESKRIPSI CAPAIAN PEMBELAJARAN (CP)</th>
                            <th class="px-4 py-3 border border-slate-200 w-28 text-center">AKSI</th>
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

                                        {{-- 5. KOLOM AKSI (EDIT & HAPUS) --}}
                                        <td class="px-4 py-3 border border-slate-200 text-center bg-white align-middle">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Tombol Edit -->
                                                <button onclick="openEditModal({{ json_encode($cp) }})"
                                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center shadow-sm"
                                                    title="Edit CP">
                                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <form action="{{ route('superadmin.capaian-pembelajaran.destroy', $cp->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data CP ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition flex items-center justify-center shadow-sm"
                                                        title="Hapus CP">
                                                        <i class="fa-solid fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400 bg-slate-50">
                                    Tidak ada data Capaian Pembelajaran untuk kriteria ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Edit CP -->
        <div id="modalEdit"
            class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl relative">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Capaian Pembelajaran</h3>
                <form id="formEdit" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Mata Pelajaran <span
                                    class="text-rose-500">*</span></label>
                            <select id="edit_mapel_id" name="mapel_id" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                @foreach($mapels as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Fase <span
                                    class="text-rose-500">*</span></label>
                            <select id="edit_fase" name="fase" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="A">Fase A</option>
                                <option value="B">Fase B</option>
                                <option value="C">Fase C</option>
                                <option value="D">Fase D</option>
                                <option value="E">Fase E</option>
                                <option value="F">Fase F</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Elemen CP <span
                                class="text-rose-500">*</span></label>
                        <input type="text" id="edit_elemen" name="elemen" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Deskripsi Capaian Pembelajaran
                            <span class="text-rose-500">*</span></label>
                        <textarea id="edit_deskripsi_cp" name="deskripsi_cp" rows="4" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-slate-600 text-sm font-semibold hover:bg-slate-100 rounded-xl">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700">Perbarui
                            CP</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Animasi Pemindai AI / Processing Overlay -->
        <div id="aiScanningModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm hidden">
            <div
                class="bg-white border border-slate-200 p-8 rounded-2xl shadow-2xl max-w-md w-full mx-4 text-center relative overflow-hidden">
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

                <h3 class="text-lg font-bold text-slate-900 mb-2">Memindai & Menyiapkan Pratinjau</h3>
                <p id="scanningStatusText" class="text-xs text-slate-500 font-medium leading-relaxed mb-6">
                    AI sedang menganalisis struktur teks dan menyusun pratinjau elemen Kurikulum Merdeka...
                </p>

                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mb-2">
                    <div id="scanningProgressBar"
                        class="bg-emerald-600 h-full w-0 transition-all duration-300 rounded-full">
                    </div>
                </div>
                <span id="scanningProgressPercent" class="text-[11px] font-bold text-emerald-600">0%</span>
            </div>
        </div>

        <!-- Script JavaScript -->
        <script>
            function openEditModal(data) {
                document.getElementById('formEdit').action = `/superadmin/capaian-pembelajaran/${data.id}`;
                document.getElementById('edit_mapel_id').value = data.mapel_id;
                document.getElementById('edit_fase').value = data.fase;
                document.getElementById('edit_elemen').value = data.elemen;
                document.getElementById('edit_deskripsi_cp').value = data.deskripsi_cp;

                const modal = document.getElementById('modalEdit');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeEditModal() {
                const modal = document.getElementById('modalEdit');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.getElementById('uploadForm').addEventListener('submit', function (e) {
                const modal = document.getElementById('aiScanningModal');
                const progressBar = document.getElementById('scanningProgressBar');
                const progressPercent = document.getElementById('scanningProgressPercent');
                const statusText = document.getElementById('scanningStatusText');

                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');

                modal.classList.remove('hidden');

                let progress = 0;
                const statuses = [
                    "Menghubungkan ke Mesin Analisis AI...",
                    "Memilah Elemen dan Format Runtut...",
                    "Menerapkan Aturan Kurikulum & Validasi...",
                    "Menyiapkan Halaman Pratinjau..."
                ];

                const interval = setInterval(function () {
                    progress += Math.floor(Math.random() * 8) + 4;
                    if (progress > 92) progress = 92;

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