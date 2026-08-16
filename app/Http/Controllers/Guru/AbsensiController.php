<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\ProfilSekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman utama absensi & filter siswa.
     */
    public function index(Request $request)
    {
        $profilSekolah = ProfilSekolah::first();
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();
        
        // Daftar mata pelajaran harian
        $mapels = [
            'Pendidikan Agama',
            'Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'IPAS',
            'PJOK',
            'Seni Budaya',
            'Bahasa Inggris',
            'Bahasa Jawa',
        ];

        $siswas = collect();
        $absensiExisting = [];

        // Jika guru telah memilih kelas
        if ($request->filled('kelas_id')) {
            $tanggal = $request->input('tanggal', date('Y-m-d'));
            $kelasId = $request->input('kelas_id');
            $mapel   = $request->input('mapel');

            // 1. Ambil data siswa dengan penanganan sorting fleksibel (nama / nama_lengkap)
            $siswas = Siswa::where('kelas_id', $kelasId)
                ->get()
                ->sortBy(function ($siswa) {
                    return $siswa->nama_lengkap ?? $siswa->nama ?? $siswa->nama_siswa ?? '';
                })
                ->values();

            // 2. Ambil data absensi yang sudah ada di tanggal & kelas tersebut
            $queryAbsensi = Absensi::where('tanggal', $tanggal)
                ->where('kelas_id', $kelasId);

            if ($mapel) {
                $queryAbsensi->where('mapel', $mapel);
            }

            // Keystoping berdasarkan siswa_id agar mudah diakses di Blade ($absensiExisting[$siswa->id])
            $absensiExisting = $queryAbsensi->get()->keyBy('siswa_id');
        }

        return view('guru.absensi.index', compact(
            'profilSekolah',
            'kelases',
            'mapels',
            'siswas',
            'absensiExisting'
        ));
    }

    /**
     * Menyimpan atau memperbarui data absensi siswa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'kelas_id' => 'required|exists:kelases,id',
            'absensi'  => 'required|array',
        ]);

        $tanggal = $request->tanggal;
        $kelasId = $request->kelas_id;
        $mapel   = $request->mapel;

        foreach ($request->absensi as $siswaId => $data) {
            // Abaikan jika status absensi tidak terpilih
            if (!isset($data['status'])) {
                continue;
            }

            // Simpan atau Perbarui (updateOrCreate)
            Absensi::updateOrCreate(
                [
                    'tanggal'  => $tanggal,
                    'kelas_id' => $kelasId,
                    'siswa_id' => $siswaId,
                    'mapel'    => $mapel,
                ],
                [
                    'status'     => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Data presensi siswa berhasil disimpan!');
    }

    /**
     * Menampilkan rekap absensi bulanan.
     */
    public function rekap(Request $request)
    {
        $profilSekolah = ProfilSekolah::first();
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();

        $bulan   = $request->input('bulan', date('m'));
        $tahun   = $request->input('tahun', date('Y'));
        $kelasId = $request->input('kelas_id');

        $rekaps = collect();

        if ($kelasId) {
            $rekaps = Siswa::where('kelas_id', $kelasId)
                ->with(['absensis' => function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun);
                }])
                ->get()
                ->sortBy(function ($siswa) {
                    return $siswa->nama_lengkap ?? $siswa->nama ?? $siswa->nama_siswa ?? '';
                })
                ->values();
        }

        return view('guru.absensi.rekap', compact(
            'profilSekolah',
            'kelases',
            'rekaps',
            'bulan',
            'tahun'
        ));
    }
}