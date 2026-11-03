<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\AlokasiWaktu;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;

class JadwalPelajaranController extends Controller
{
    /**
     * Menampilkan halaman utama jadwal pelajaran, alokasi waktu, dan daftar guru
     */
    public function index(Request $request)
    {
        $kelasAktif = $request->get('kelas', 'Kelas 1');
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $gurus = Guru::orderBy('nama_lengkap', 'asc')->get();

        // Ambil data alokasi waktu per mapel untuk kelas terpilih
        $alokasis = AlokasiWaktu::where('kelas', $kelasAktif)->get()->keyBy('mapel_id');

        // Ambil daftar jadwal manual per baris yang sudah diinput untuk kelas ini
        $jadwalsList = JadwalPelajaran::where('kelas', $kelasAktif)->orderBy('hari')->orderBy('jam_ke')->get();

        // Hitung total target jam vs total jadwal terisi
        $totalTargetJam = $alokasis->sum('jumlah_jam');
        $totalJadwalTerisi = $jadwalsList->count();

        return view('admin.jadwal.index', compact('mapels', 'kelasAktif', 'alokasis', 'jadwalsList', 'gurus', 'totalTargetJam', 'totalJadwalTerisi'));
    }

    /**
     * Menyimpan atau memperbarui target alokasi waktu per minggu
     */
    public function saveAlokasi(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'alokasi' => 'required|array',
        ]);

        foreach ($request->alokasi as $mapelId => $jumlahJam) {
            AlokasiWaktu::updateOrCreate(
                ['kelas' => $request->kelas, 'mapel_id' => $mapelId],
                ['jumlah_jam' => (int)$jumlahJam]
            );
        }

        return back()->with('success', 'Target alokasi waktu berhasil disimpan.');
    }

    /**
     * Mengatur alokasi waktu secara massal/serentak untuk semua mapel
     * (Kecuali PAI dan PJOK)
     */
    public function saveAlokasiMassal(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'default_jam' => 'required|integer|min:1',
        ]);

        $kelas = $request->kelas;
        $defaultJam = (int) $request->default_jam;

        $mapels = Mapel::whereNotIn('nama_mapel', [
            'Pendidikan Agama Islam',
            'PAI',
            'PJOK',
            'Pendidikan Jasmani, Olahraga, dan Kesehatan'
        ])->get();

        foreach ($mapels as $mapel) {
            AlokasiWaktu::updateOrCreate(
                ['kelas' => $kelas, 'mapel_id' => $mapel->id],
                ['jumlah_jam' => $defaultJam]
            );
        }

        return back()->with('success', 'Alokasi waktu default berhasil diterapkan untuk semua mapel (kecuali Agama & Olahraga).');
    }

    /**
     * Menyimpan sesi jadwal pelajaran manual per baris dengan validasi ketat
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'wali_kelas' => 'required|string',
            'hari' => 'required|string',
            'jam_ke' => 'required|integer|min:1|max:12',
            'mapel_id' => 'required|exists:mapels,id',
        ]);

        $kelas = $request->kelas;
        $waliKelas = $request->wali_kelas;
        $hari = $request->hari;
        $jamKe = $request->jam_ke;
        $mapelId = $request->mapel_id;

        $mapel = Mapel::find($mapelId);
        $namaMapelLOWER = strtolower($mapel->nama_mapel);

        // 1. VALIDASI HARI SENIN JAM KE-1 (KHUSUS UPACARA BENDERA)
        if ($hari === 'Senin' && $jamKe == 1) {
            if (!str_contains($namaMapelLOWER, 'upacara')) {
                return back()->withInput()->with('error', 'Gagal Menyimpan! Hari Senin Jam ke-1 dialokasikan khusus untuk Upacara Bendera.');
            }
        }

        // 2. VALIDASI BENTROK MAPEL KHUSUS (PAI & PJOK) DI SELURUH KELAS
        $isPAI  = str_contains($namaMapelLOWER, 'agama') || str_contains($namaMapelLOWER, 'pai');
        $isPJOK = str_contains($namaMapelLOWER, 'pjok') || str_contains($namaMapelLOWER, 'olahraga');

        if ($isPAI || $isPJOK) {
            $cekBentrokKhusus = JadwalPelajaran::where('kelas', '!=', $kelas)
                ->where('hari', $hari)
                ->where('jam_ke', $jamKe)
                ->whereHas('mapel', function ($query) use ($isPAI) {
                    if ($isPAI) {
                        $query->whereRaw("LOWER(nama_mapel) LIKE '%agama%' OR LOWER(nama_mapel) LIKE '%pai%'");
                    } else {
                        $query->whereRaw("LOWER(nama_mapel) LIKE '%pjok%' OR LOWER(nama_mapel) LIKE '%olahraga%'");
                    }
                })
                ->exists();

            if ($cekBentrokKhusus) {
                $kategori = $isPAI ? 'Agama / PAI' : 'PJOK / Olahraga';
                return back()->withInput()->with('error', "Gagal Menyimpan! Mata pelajaran {$kategori} sudah dijadwalkan di kelas lain pada hari {$hari} jam ke-{$jamKe}.");
            }
        }

        // 3. VALIDASI BENTROK GURU/WALI KELAS YANG SAMA DI DUA KELAS BERBEDA
        $cekBentrokGuru = JadwalPelajaran::where('kelas', '!=', $kelas)
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->where('wali_kelas', $waliKelas)
            ->exists();

        if ($cekBentrokGuru) {
            return back()->withInput()->with('error', "Gagal Menyimpan! Guru '{$waliKelas}' sudah mengajar di kelas lain pada hari {$hari} jam ke-{$jamKe}.");
        }

        // 3. VALIDASI SLOT JAM TERISI DI KELAS INI
        $sudahTerisi = JadwalPelajaran::where('kelas', $kelas)
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->exists();

        if ($sudahTerisi) {
            return back()->withInput()->with('error', "Slot jam ke-{$jamKe} pada hari {$hari} di kelas ini sudah terisi mata pelajaran lain!");
        }

        // 4. VALIDASI TARGET ALOKASI WAKTU MINGGUAN
        $alokasiTarget = AlokasiWaktu::where('kelas', $kelas)->where('mapel_id', $mapelId)->first();
        $maxJam = $alokasiTarget ? $alokasiTarget->jumlah_jam : 0;

        if ($maxJam <= 0) {
            return back()->withInput()->with('error', "Gagal! Target alokasi waktu untuk mata pelajaran '{$mapel->nama_mapel}' belum diatur (0 Jam). Silakan atur target alokasi terlebih dahulu di Bagian 1.");
        }

        $currentCount = JadwalPelajaran::where('kelas', $kelas)->where('mapel_id', $mapelId)->count();
        if ($currentCount >= $maxJam) {
            return back()->withInput()->with('error', "Gagal! Mata pelajaran '{$mapel->nama_mapel}' sudah memenuhi batas alokasi target ({$maxJam} jam/minggu) untuk kelas ini.");
        }

        // Simpan sesi jadwal baru
        JadwalPelajaran::create([
            'kelas' => $kelas,
            'wali_kelas' => $waliKelas,
            'hari' => $hari,
            'jam_ke' => $jamKe,
            'mapel_id' => $mapelId,
        ]);

        return back()->with('success', 'Sesi jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Menghapus sesi jadwal satuan
     */
    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Sesi jadwal berhasil dihapus.');
    }

    /**
     * Menampilkan halaman cetak jadwal pelajaran sesuai format resmi
     */
    public function cetak(Request $request)
    {
        $kelasAktif = $request->get('kelas', 'Kelas 1');
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();

        // Ambil data alokasi waktu per mapel untuk kelas terpilih
        $alokasis = AlokasiWaktu::where('kelas', $kelasAktif)->get()->keyBy('mapel_id');
        $totalTargetJam = $alokasis->sum('jumlah_jam');

        // Ambil data jadwal yang sudah tersimpan
        $jadwalsList = JadwalPelajaran::where('kelas', $kelasAktif)->get();
        $totalJadwalTerisi = $jadwalsList->count();

        // Notifikasi peringatan jika jadwal terisi belum pas dengan total target
        if ($totalTargetJam > 0 && $totalJadwalTerisi < $totalTargetJam) {
            $kekurangan = $totalTargetJam - $totalJadwalTerisi;
            session()->flash('warning', "Peringatan: Jumlah sesi jadwal terisi baru {$totalJadwalTerisi} jam dari target {$totalTargetJam} jam (kurang {$kekurangan} jam).");
        }

        $jadwals = $jadwalsList->groupBy(function ($item) {
            return $item->hari . '_' . $item->jam_ke;
        });

        // Ambil tahun ajaran yang sedang aktif di database
        $tahunAjaranAktif = TahunAjaran::getAktif();

        return view('admin.jadwal.cetak', compact('mapels', 'kelasAktif', 'alokasis', 'jadwals', 'tahunAjaranAktif', 'totalTargetJam', 'totalJadwalTerisi'));
    }
}
