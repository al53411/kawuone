@extends('layouts.admin')

@section('title', 'Master Capaian Pembelajaran')
@section('page_title', 'Master Capaian Pembelajaran')

@section('content')
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>

    <div class="w-full bg-white text-slate-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Master Capaian Pembelajaran
                    (CP)</h1>
                <p class="text-slate-500 text-sm mt-1">Pengaturan standar Capaian Pembelajaran Kurikulum Merdeka (Mencakup
                    data pusat dan sekolah)</p>
            </div>
            <button onclick="openModal('modalAdd')"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-all shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah CP Baru
            </button>
        </div>

        <!-- Notification -->
        @if(session('success'))
            <div
                class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check mr-2 text-base text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()"
                    class="text-emerald-500 hover:text-emerald-800">&times;</button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="glass-card p-4 rounded-2xl mb-6 shadow-md border border-slate-200">
            <form method="GET" action="{{ route('admin.capaian-pembelajaran.index') }}"
                class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mata Pelajaran</label>
                    <select name="mapel_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fase Pembelajaran</label>
                    <select name="fase" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Fase --</option>
                        @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $f)
                            <option value="{{ $f }}" {{ request('fase') == $f ? 'selected' : '' }}>Fase {{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('admin.capaian-pembelajaran.index') }}"
                        class="w-full py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-sm text-center transition-all">Reset
                        Filter</a>
                </div>
            </form>
        </div>

        <!-- Data Tabel dengan Layout Rowspan Sesuai Standar Kurikulum -->
        <div class="border border-slate-200 rounded-2xl shadow-sm overflow-hidden bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                            <th class="p-4 w-32 text-center border border-slate-700">Fase</th>
                            <th class="p-4 w-48 text-center border border-slate-700">Mata Pelajaran</th>
                            <th class="p-4 w-52 border border-slate-700">Elemen</th>
                            <th class="p-4 border border-slate-700">Deskripsi Capaian Pembelajaran</th>
                            <th class="p-4 w-28 text-center border border-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @php
                            // Kelompokkan data berdasarkan fase dan mapel_id untuk penerapan rowspan
                            $groupedCps = $cps->getCollection()->groupBy([
                                'fase',
                                function ($item) {
                                    return $item->mapel_id;
                                }
                            ]);
                        @endphp

                        @forelse($groupedCps as $fase => $mapelGroups)
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

                                @foreach($items as $index => $item)
                                    <tr class="hover:bg-slate-50 transition-all">
                                        {{-- 1. Kolom Fase (Rowspan) --}}
                                        @if($loop->parent->first && $loop->first)
                                            <td rowspan="{{ $faseRowspan }}"
                                                class="p-4 border border-slate-200 text-center font-black bg-slate-900 text-white align-middle">
                                                <div class="flex flex-col items-center justify-center space-y-1">
                                                    <span
                                                        class="bg-emerald-500 text-slate-950 text-xs px-2.5 py-1 rounded-md uppercase font-extrabold">
                                                        Fase {{ strtoupper($fase) }}
                                                    </span>
                                                    <span class="text-[11px] text-slate-300 font-normal">
                                                        @if(strtoupper($fase) == 'A') (Kelas 1-2)
                                                        @elseif(strtoupper($fase) == 'B') (Kelas 3-4)
                                                        @elseif(strtoupper($fase) == 'C') (Kelas 5-6)
                                                        @elseif(strtoupper($fase) == 'D') (Kelas 7-9)
                                                        @elseif(strtoupper($fase) == 'E') (Kelas 10)
                                                        @elseif(strtoupper($fase) == 'F') (Kelas 11-12)
                                                        @else (Umum)
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                        @endif

                                        {{-- 2. Kolom Mata Pelajaran (Rowspan) --}}
                                        @if($loop->first)
                                            <td rowspan="{{ $mapelRowspan }}"
                                                class="p-4 border border-slate-200 font-bold text-slate-800 align-middle bg-slate-50 text-center">
                                                {{ $mapelNama }}
                                            </td>
                                        @endif

                                        {{-- 3. Kolom Elemen --}}
                                        <td class="p-4 border border-slate-200 font-semibold text-slate-700 bg-white">
                                            <span
                                                class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs inline-block">
                                                {{ $item->elemen }}
                                            </span>
                                        </td>

                                        {{-- 4. Kolom Deskripsi CP --}}
                                        <td
                                            class="p-4 border border-slate-200 text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-white">
                                            {{ $item->deskripsi_cp }}
                                            @if(is_null($item->sekolah_id))
                                                <span
                                                    class="ml-2 inline-block px-2 py-0.5 bg-indigo-50 text-indigo-600 border border-indigo-200 text-[10px] rounded font-bold">Pusat</span>
                                            @endif
                                        </td>

                                        {{-- 5. Kolom Aksi --}}
                                        <td class="p-4 border border-slate-200 text-center bg-white">
                                            <div class="flex justify-center gap-2">
                                                <button onclick="editModal({{ $item }})"
                                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                {{-- Sembunyikan tombol hapus jika data milik pusat dan user adalah sekolah (opsional,
                                                sesuaikan kebutuhan) --}}
                                                @if(!is_null($item->sekolah_id))
                                                    <form action="{{ route('admin.capaian-pembelajaran.destroy', $item->id) }}"
                                                        method="POST" onsubmit="return confirm('Yakin hapus data CP ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">Belum ada data Capaian Pembelajaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($cps, 'hasPages') && $cps->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $cps->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah CP -->
    <div id="modalAdd" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl relative">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Capaian Pembelajaran</h3>
            <form action="{{ route('admin.capaian-pembelajaran.store') }}" method="POST" class="space-y-4" id="formAddCp">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Mata Pelajaran <span
                                class="text-rose-500">*</span></label>
                        <select name="mapel_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Fase <span
                                class="text-rose-500">*</span></label>
                        <select name="fase" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- Pilih Fase --</option>
                            <option value="A">Fase A (Kelas 1-2 SD)</option>
                            <option value="B">Fase B (Kelas 3-4 SD)</option>
                            <option value="C">Fase C (Kelas 5-6 SD)</option>
                            <option value="D">Fase D (SMP)</option>
                            <option value="E">Fase E (SMA/SMK 10)</option>
                            <option value="F">Fase F (SMA/SMK 11-12)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Elemen CP <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="elemen" required
                        placeholder="Contoh: Al-Qur'an Hadis / Pemahaman IPAS / Geometri"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Deskripsi Capaian Pembelajaran
                        <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi_cp" rows="4" required
                        placeholder="Tuliskan teks narasi Capaian Pembelajaran secara lengkap..."
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modalAdd')"
                        class="px-4 py-2 text-slate-600 text-sm font-semibold hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" id="submitAddBtn"
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 flex items-center gap-2 transition-all">
                        <span id="addBtnIcon">💾</span>
                        <span id="addBtnText">Simpan CP</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit CP -->
    <div id="modalEdit" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
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
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 text-slate-600 text-sm font-semibold hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700">Perbarui
                        CP</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }
        function editModal(data) {
            document.getElementById('formEdit').action = `/admin/capaian-pembelajaran/${data.id}`;
            document.getElementById('edit_mapel_id').value = data.mapel_id;
            document.getElementById('edit_fase').value = data.fase;
            document.getElementById('edit_elemen').value = data.elemen;
            document.getElementById('edit_deskripsi_cp').value = data.deskripsi_cp;
            openModal('modalEdit');
        }

        // Animasi Loading/Proses pada Form Tambah
        document.getElementById('formAddCp').addEventListener('submit', function (e) {
            const btn = document.getElementById('submitAddBtn');
            const btnText = document.getElementById('addBtnText');
            const btnIcon = document.getElementById('addBtnIcon');

            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            btnIcon.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            btnText.innerText = "Menyimpan Data...";
        });
    </script>
@endsection