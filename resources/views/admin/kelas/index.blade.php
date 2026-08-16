@extends('layouts.admin')

@section('title', 'Data Kelas')
@section('page_title', 'Data Kelas')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Kelas</h1>
    <p class="text-sm text-gray-500 mt-1">Tambah dan kelola daftar kelas
        {{ Auth::user()?->sekolah?->nama_sekolah ?? $profilSekolah?->nama_sekolah ?? 'Sekolah' }}.</p>
</div>

@if(session('success'))
<div
    class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 text-sm rounded flex items-center justify-between">
    <div>
        <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 text-sm rounded flex items-center justify-between">
    <div>
        <i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ session('error') }}
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Form Tambah Kelas --}}
    <div class="flat-card p-6 bg-white h-fit shadow-sm rounded-lg border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Kelas Baru</h2>
        <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kelas</label>
                <input type="text" name="nama_kelas" class="w-full px-4 py-2 flat-input border rounded-md"
                    placeholder="Contoh: Kelas 1, Kelas 2-A" value="{{ old('nama_kelas') }}" required>
                @error('nama_kelas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Wali Kelas</label>
                <select name="guru_id" class="w-full px-4 py-2 flat-input bg-white border rounded-md">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($gurus as $guru)
                    @php
                    $namaGuru = $guru->nama_guru ?? $guru->nama_lengkap ?? $guru->nama ?? $guru->name ?? ('Guru
                    #'.$guru->id);
                    @endphp
                    <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                        {{ $namaGuru }}
                    </option>
                    @endforeach
                </select>
                @error('guru_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section Checkbox Guru Pengampu --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Guru Pengampu / Mengajar</label>
                <div class="space-y-2 max-h-40 overflow-y-auto p-3 border rounded-md bg-gray-50/50">
                    @forelse($gurus as $guru)
                    @php
                    $namaGuru = $guru->nama_guru ?? $guru->nama_lengkap ?? $guru->nama ?? $guru->name ?? ('Guru
                    #'.$guru->id);
                    @endphp
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" name="guru_ids[]" value="{{ $guru->id }}"
                            {{ is_array(old('guru_ids')) && in_array($guru->id, old('guru_ids')) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ $namaGuru }}</span>
                    </label>
                    @empty
                    <p class="text-xs text-gray-400 italic">Belum ada data guru.</p>
                    @endforelse
                </div>
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded transition shadow-sm">
                <i class="fa-solid fa-plus mr-1.5"></i> Simpan Kelas
            </button>
        </form>
    </div>

    {{-- Tabel Data Kelas --}}
    <div class="lg:col-span-2 flat-card overflow-hidden bg-white shadow-sm rounded-lg border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-16">No</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Nama Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Wali Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kelas as $index => $k)
                    @php
                    $namaWali = $k->waliKelas?->nama_guru
                    ?? $k->waliKelas?->nama_lengkap
                    ?? $k->waliKelas?->nama
                    ?? $k->waliKelas?->name
                    ?? $k->wali_kelas;

                    // Ambil array ID guru pengampu untuk dikirim ke JS Modal
                    $assignedGuruIds = $k->gurus ? $k->gurus->pluck('id')->toArray() : [];
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500 text-center font-medium">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $k->nama_kelas }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($namaWali)
                            <span class="inline-flex items-center text-gray-700">
                                <i class="fa-solid fa-user-tie text-gray-400 mr-2 text-xs"></i>
                                {{ $namaWali }}
                            </span>
                            @else
                            <span class="text-xs text-gray-400 italic">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- Tombol Edit --}}
                                <button type="button"
                                    onclick="openEditModal('{{ route('admin.kelas.update', $k->id) }}', '{{ addslashes($k->nama_kelas) }}', '{{ $k->guru_id }}', {{ json_encode($assignedGuruIds) }})"
                                    class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded text-xs font-semibold transition border border-amber-200">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus kelas {{ addslashes($k->nama_kelas) }}?')"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded text-xs font-semibold transition border border-red-200">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                            Belum ada data kelas. Silakan input di form sebelah kiri.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit Kelas --}}
<div id="editModal"
    class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Edit Data Kelas</h3>
            <button type="button" onclick="closeEditModal()"
                class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kelas</label>
                <input type="text" id="edit_nama_kelas" name="nama_kelas"
                    class="w-full px-4 py-2 border rounded-md flat-input" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Wali Kelas</label>
                <select id="edit_guru_id" name="guru_id" class="w-full px-4 py-2 border rounded-md flat-input bg-white">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($gurus as $guru)
                    @php
                    $namaGuru = $guru->nama_guru ?? $guru->nama_lengkap ?? $guru->nama ?? $guru->name ?? ('Guru
                    #'.$guru->id);
                    @endphp
                    <option value="{{ $guru->id }}">{{ $namaGuru }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Checkbox Guru Pengampu pada Modal --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Guru Pengampu / Mengajar</label>
                <div class="space-y-2 max-h-40 overflow-y-auto p-3 border rounded-md bg-gray-50/50">
                    @foreach($gurus as $guru)
                    @php
                    $namaGuru = $guru->nama_guru ?? $guru->nama_lengkap ?? $guru->nama ?? $guru->name ?? ('Guru
                    #'.$guru->id);
                    @endphp
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" name="guru_ids[]" value="{{ $guru->id }}"
                            class="edit-guru-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ $namaGuru }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-3">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Pendukung Modal Edit --}}
<script>
function openEditModal(url, namaKelas, guruId, assignedGuruIds = []) {
    document.getElementById('editForm').action = url;
    document.getElementById('edit_nama_kelas').value = namaKelas;
    document.getElementById('edit_guru_id').value = (guruId && guruId !== 'null') ? guruId : '';

    // Centang otomatis checkbox guru pengampu sesuai data kelas
    const checkboxes = document.querySelectorAll('.edit-guru-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = assignedGuruIds.includes(parseInt(cb.value));
    });

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection