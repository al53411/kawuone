<form action="{{ route('guru.jurnal.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    @csrf

    <style>
    .glass-card {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
</style>

<div class="w-full bg-white text-slate-800 p-4 sm:p-6">
    <div class="w-full bg-white text-slate-800 p-4 sm:p-6">
    <form action="{{ route('guru.jurnal.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Tanggal, Kelas, Jam Ke -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Mengajar</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kelas</label>
                <select name="kelas_id" required 
                    class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelass as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pertemuan / Jam Ke</label>
                <input type="text" name="jam_ke" placeholder="Contoh: Jam ke 1-2 (07.00 - 08.10)" required
                    class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
        </div>

        <!-- Mata Pelajaran & Materi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mata Pelajaran</label>
                <input type="text" name="mapel" placeholder="Contoh: Matematika / IPAS" required
                    class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Materi / Bab Pembelajaran</label>
                <input type="text" name="materi" placeholder="Contoh: Bab 2 - Operasi Hitung Perkalian" required
                    class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
        </div>

        <!-- Kegiatan Pembelajaran -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kegiatan Pembelajaran</label>
            <textarea name="kegiatan" rows="4" required placeholder="Jelaskan ringkasan kegiatan pembelajaran di kelas..."
                class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
        </div>

        <!-- Keterangan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan (Opsional)</label>
            <input type="text" name="keterangan" placeholder="Contoh: Terlaksana, 2 siswa izin, atau Latihan Soal"
                class="w-full px-4 py-2.5 bg-white/80 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
        </div>

        <!-- Tombol Simpan -->
        <div class="pt-2">
            <button type="submit" 
                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm shadow-lg transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Simpan Jurnal Mengajar
            </button>
        </div>
    </form>
</div>
</div>


</form>