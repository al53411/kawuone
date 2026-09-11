@extends('layouts.admin')

@section('title', 'Alur Tujuan Pembelajaran')
@section('page_title', 'Alur Tujuan Pembelajaran')


@section('content')
<div class="w-full bg-white text-slate-800 p-4 sm:p-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Alur Tujuan Pembelajaran (ATP)</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola urutan alur materi dan alokasi JP per semester berdasarkan Tujuan Pembelajaran (TP)</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah ATP
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-4 p-4 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 font-bold">&times;</button>
        </div>
    @endif

    <!-- Alert Error Validation -->
    @if($errors->any())
        <div class="mb-4 p-4 text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg">
            <p class="font-bold mb-1">Terjadi Kesalahan:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.atp.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Mata Pelajaran</label>
                <select name="mapel_id" onchange="this.form.submit()" class="w-full text-xs p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Kelas</label>
                <select name="kelas_id" onchange="this.form.submit()" class="w-full text-xs p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <a href="{{ route('admin.atp.index') }}" class="px-3 py-2.5 text-xs text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold uppercase">
                    <tr>
                        <th class="p-3 text-center w-12">Urutan</th>
                        <th class="p-3">Kode ATP</th>
                        <th class="p-3">Mata Pelajaran / Kelas</th>
                        <th class="p-3">Tujuan Pembelajaran (TP)</th>
                        <th class="p-3">Rincian Materi ATP</th>
                        <th class="p-3 text-center">Semester</th>
                        <th class="p-3 text-center">Alokasi JP</th>
                        <th class="p-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($atps as $atp)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-3 text-center font-bold text-indigo-600">{{ $atp->urutan }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $atp->kode_atp }}</td>
                            <td class="p-3">
                                <div class="font-medium text-slate-800">{{ $atp->mapel->nama_mapel ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $atp->kelas->nama_kelas ?? 'Semua Kelas' }}</div>
                            </td>
                            <td class="p-3 max-w-xs">
                                <span class="font-semibold text-indigo-600 block mb-0.5">{{ $atp->tujuanPembelajaran->kode_tp ?? '-' }}</span>
                                <p class="line-clamp-2 text-slate-600">{{ $atp->tujuanPembelajaran->deskripsi_tp ?? '-' }}</p>
                            </td>
                            <td class="p-3 max-w-xs">
                                <p class="line-clamp-2 text-slate-700">{{ $atp->rincian_materi }}</p>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold {{ $atp->semester == 1 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    Semester {{ $atp->semester }}
                                </span>
                            </td>
                            <td class="p-3 text-center font-medium">{{ $atp->alokasi_jp ?? 2 }} JP</td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ json_encode($atp) }})" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.atp.destroy', $atp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ATP ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400">Belum ada data Alur Tujuan Pembelajaran (ATP).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL FORM (TAMBAH / EDIT) -->
<div id="atpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-slate-100 overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 id="modalTitle" class="text-sm font-bold text-slate-800">Tambah Alur Tujuan Pembelajaran (ATP)</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <!-- Form Modal -->
        <form id="atpForm" action="{{ route('admin.atp.store') }}" method="POST">
            @csrf
            <div id="methodContainer"></div>

            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs">
                <!-- Select TP -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Tujuan Pembelajaran (TP) <span class="text-rose-500">*</span></label>
                    <select id="tujuan_pembelajaran_id" name="tujuan_pembelajaran_id" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih TP --</option>
                        @foreach($tps as $tp)
                            <option value="{{ $tp->id }}" data-mapel="{{ $tp->mapel_id }}" data-kelas="{{ $tp->kelas_id }}">
                                {{ $tp->kode_tp }} - {{ Str::limit($tp->deskripsi_tp, 80) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grid Mapel & Kelas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                        <select id="mapel_id" name="mapel_id" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kelas</label>
                        <select id="kelas_id" name="kelas_id" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Grid Kode ATP, Urutan, JP, Semester -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode ATP <span class="text-rose-500">*</span></label>
                        <input type="text" id="kode_atp" name="kode_atp" placeholder="Contoh: ATP-5.1.1" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Urutan Tahap <span class="text-rose-500">*</span></label>
                        <input type="number" id="urutan" name="urutan" min="1" value="1" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                        <select id="semester" name="semester" required class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alokasi JP</label>
                        <input type="number" id="alokasi_jp" name="alokasi_jp" min="1" value="2" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Textarea Rincian Materi -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Rincian Materi / Alur Pembelajaran <span class="text-rose-500">*</span></label>
                    <textarea id="rincian_materi" name="rincian_materi" rows="4" required placeholder="Tuliskan rincian tahapan materi pembelajaran..." class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-xs text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">Simpan ATP</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Alur Tujuan Pembelajaran (ATP)';
        document.getElementById('atpForm').action = "{{ route('admin.atp.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        
        // Reset Form Inputs
        document.getElementById('tujuan_pembelajaran_id').value = '';
        document.getElementById('mapel_id').value = '';
        document.getElementById('kelas_id').value = '';
        document.getElementById('kode_atp').value = '';
        document.getElementById('urutan').value = '1';
        document.getElementById('semester').value = '1';
        document.getElementById('alokasi_jp').value = '2';
        document.getElementById('rincian_materi').value = '';

        document.getElementById('atpModal').classList.remove('hidden');
    }

    function openEditModal(atp) {
        document.getElementById('modalTitle').innerText = 'Edit Alur Tujuan Pembelajaran (ATP)';
        document.getElementById('atpForm').action = `/admin/atp/${atp.id}`;
        document.getElementById('methodContainer').innerHTML = `@method('PUT')`;

        // Fill Form Inputs
        document.getElementById('tujuan_pembelajaran_id').value = atp.tujuan_pembelajaran_id;
        document.getElementById('mapel_id').value = atp.mapel_id;
        document.getElementById('kelas_id').value = atp.kelas_id || '';
        document.getElementById('kode_atp').value = atp.kode_atp;
        document.getElementById('urutan').value = atp.urutan;
        document.getElementById('semester').value = atp.semester;
        document.getElementById('alokasi_jp').value = atp.alokasi_jp || 2;
        document.getElementById('rincian_materi').value = atp.rincian_materi;

        document.getElementById('atpModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('atpModal').classList.add('hidden');
    }

    // Auto Selected Mapel & Kelas saat TP dipilih di Form Modal
    document.getElementById('tujuan_pembelajaran_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const mapelId = selectedOption.getAttribute('data-mapel');
        const kelasId = selectedOption.getAttribute('data-kelas');

        if (mapelId) {
            document.getElementById('mapel_id').value = mapelId;
        }
        if (kelasId) {
            document.getElementById('kelas_id').value = kelasId;
        }
    });
</script>
@endsection