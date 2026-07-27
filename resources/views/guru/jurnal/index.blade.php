@extends('layouts.guru')

@section('title', $profilSekolah->nama_sekolah ?? 'SDN Kawu 1')

@section('content')
<div class="space-y-6">

    <!-- Header Halaman & Tombol Cetak PDF -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Jurnal Pembelajaran Guru</h1>
            <p class="text-sm text-slate-500">Catatan aktivitas KBM harian yang terintegrasi dengan validasi Kepala
                Sekolah.</p>
        </div>
        <div>
            <a href="{{ route('guru.jurnal.cetak') }}" target="_blank"
                class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm transition flex items-center gap-2 shadow-sm w-fit">
                📄 Cetak PDF Rekap
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
    <div
        class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Alert Notifikasi Error / Session Failure -->
    @if(session('error'))
    <div
        class="p-4 mb-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span>🚫</span>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Alert Notifikasi Error Validation -->
    @if($errors->any())
    <div class="p-4 mb-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200">
        <div class="font-bold mb-1">⚠️ Terjadi kesalahan input:</div>
        <ul class="list-disc list-inside space-y-1 text-xs">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Input Jurnal -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span>📝</span> Input Jurnal Mengajar Baru
        </h2>

        <form action="{{ route('guru.jurnal.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mengajar</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kelas</label>
                    <select name="kelas_id" required
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Pelajaran</label>
                    <select name="mapel" required
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapels as $mapel)
                        <option value="{{ $mapel }}" {{ old('mapel') == $mapel ? 'selected' : '' }}>
                            {{ $mapel }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jam Ke -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Ke-</label>
                    <input type="text" name="jam_ke" value="{{ old('jam_ke') }}"
                        placeholder="Contoh: 1-2 (07.00 - 08.10)" required
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Materi / TP -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Materi / TP Pembelajaran</label>
                    <textarea name="materi" rows="3" placeholder="Tuliskan materi pembelajaran hari ini..." required
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">{{ old('materi') }}</textarea>
                </div>

                <!-- Kegiatan Pembelajaran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kegiatan Pembelajaran</label>
                    <textarea name="kegiatan" rows="3" placeholder="Deskripsi aktivitas/metode pembelajaran..." required
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">{{ old('kegiatan') }}</textarea>
                </div>
            </div>

            <!-- Keterangan / Catatan Selesai -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                    placeholder="Contoh: Selesai / Dilanjutkan minggu depan"
                    class="w-full text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Tombol Simpan -->
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    💾 Simpan Jurnal
                </button>
            </div>

        </form>
    </div>

    <!-- Riwayat Jurnal -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span>📖</span> Riwayat Jurnal Pembelajaran
        </h2>

        <div class="overflow-x-auto">
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
                    @forelse($jurnals as $jurnal)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-4 font-medium text-slate-900 whitespace-nowrap">
                            <div class="font-bold text-slate-800">{{ $jurnal->hari }}</div>
                            <div class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}</div>
                            <div class="text-xs text-indigo-600">Jam: {{ $jurnal->jam_ke }}</div>
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
                            @if($jurnal->status_validasi === 'Disetujui')
                            <span class="px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                                Disetujui
                            </span>
                            @elseif($jurnal->status_validasi === 'Ditolak')
                            <span class="px-3 py-1 text-xs font-semibold bg-rose-100 text-rose-800 rounded-full">
                                Ditolak
                            </span>
                            @else
                            <span class="px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                                Pending
                            </span>
                            @endif
                        </td>
                        <!-- Kolom Aksi Hapus -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            @if($jurnal->status_validasi !== 'Disetujui')
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
                            <span class="text-slate-300 text-xs">🔒 Terkunci</span>
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

</div>
@endsection