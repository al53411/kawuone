<form action="{{ route('guru.jurnal.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Tanggal -->
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Mengajar</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
        </div>

        <!-- Kelas -->
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kelas</label>
            <select name="kelas_id" required class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelass as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <!-- Pertemuan / Jam Ke -->
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pertemuan / Jam Ke</label>
            <input type="text" name="jam_ke" placeholder="Contoh: Jam ke 1-2 (07.00 - 08.10)" required
                class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <!-- Mata Pelajaran & Materi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Mata Pelajaran</label>
            <input type="text" name="mapel" placeholder="Contoh: Matematika / IPAS" required
                class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Materi / Bab Pembelajaran</label>
            <input type="text" name="materi" placeholder="Contoh: Bab 2 - Operasi Hitung Perkalilan" required
                class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <!-- Kegiatan Pembelajaran -->
    <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kegiatan Pembelajaran</label>
        <textarea name="kegiatan" rows="3" required placeholder="Jelaskan ringkasan kegiatan pembelajaran di kelas..."
            class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
    </div>

    <!-- Keterangan -->
    <div>
        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan (Opsional)</label>
        <input type="text" name="keterangan" placeholder="Contoh: Terlaksana, 2 siswa izin, atau Latihan Soal"
            class="w-full px-3 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
    </div>

    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm shadow transition">
        <i class="fa-solid fa-paper-plane mr-1"></i> Simpan Jurnal Mengajar
    </button>
</form>