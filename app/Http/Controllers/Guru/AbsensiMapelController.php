<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\AbsensiMapel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AbsensiMapelController extends Controller
{
    private function getSekolahId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        return $user->sekolah_id ?? $user->guru?->sekolah_id;
    }

    public function index(Request $request)
    {
        $sekolahId   = $this->getSekolahId();
        $tanggal     = $request->get('tanggal', Carbon::today()->toDateString());
        $kelasId     = $request->get('kelas_id');
        $mapelId     = $request->get('mapel_id');
        $pertemuanKe = $request->get('pertemuan_ke', 1);

        // Ambil SEMUA KELAS untuk Guru Mapel
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }
        $kelases = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        // Ambil SEMUA MATA PELAJARAN
        $mapelQuery = Mapel::query();
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $mapelQuery->where('sekolah_id', $sekolahId);
        }
        $mapels = $mapelQuery->orderBy('nama_mapel', 'asc')->get();

        $siswas = collect();
        $absensiExisting = [];

        if ($kelasId && $mapelId) {
            $siswaQuery = Siswa::where('kelas_id', $kelasId);

            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $siswaQuery->where(function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }

            // Ambil data siswa lalu urutkan via Collection agar aman dari perbedaan nama kolom database
            $siswas = $siswaQuery->get()
                ->sortBy(function ($siswa) {
                    return $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '';
                })
                ->values();

            // Ambil data absensi mapel yang sudah ada
            $queryAbsensi = AbsensiMapel::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->where('pertemuan_ke', $pertemuanKe)
                ->where('tanggal', $tanggal);

            if ($sekolahId && Schema::hasColumn('absensi_mapels', 'sekolah_id')) {
                $queryAbsensi->where('sekolah_id', $sekolahId);
            }

            $absensiExisting = $queryAbsensi->get()->keyBy('siswa_id');
        }

        return view('guru.absensi_mapel.index', compact(
            'kelases', 'mapels', 'siswas', 'tanggal', 'kelasId', 'mapelId', 'pertemuanKe', 'absensiExisting'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'     => 'required|exists:kelas,id',
            'mapel_id'     => 'required|exists:mapels,id',
            'tanggal'      => 'required|date',
            'pertemuan_ke' => 'required|integer',
            'absensi'      => 'required|array',
        ]);

        $user      = Auth::user();
        $guruId    = $user?->guru?->id ?? null;
        $sekolahId = $this->getSekolahId();

        DB::transaction(function () use ($request, $guruId, $sekolahId) {
            foreach ($request->absensi as $siswaId => $data) {
                $status     = $data['status'] ?? 'Hadir';
                $keterangan = $data['keterangan'] ?? null;

                $matchConditions = [
                    'siswa_id'     => $siswaId,
                    'kelas_id'     => $request->kelas_id,
                    'mapel_id'     => $request->mapel_id,
                    'pertemuan_ke' => $request->pertemuan_ke,
                ];

                $updateData = [
                    'guru_id'    => $guruId,
                    'tanggal'    => $request->tanggal,
                    'status'     => $status,
                    'keterangan' => $keterangan,
                ];

                if ($sekolahId && Schema::hasColumn('absensi_mapels', 'sekolah_id')) {
                    $updateData['sekolah_id'] = $sekolahId;
                }

                AbsensiMapel::updateOrCreate($matchConditions, $updateData);
            }
        });

        return redirect()->back()->with('success', 'Absensi Mata Pelajaran berhasil disimpan!');
    }
    
    /**
     * Menampilkan halaman rekap absensi mata pelajaran
     */
    public function rekap(Request $request)
    {
        $sekolahId   = $this->getSekolahId();
        $kelasId     = $request->get('kelas_id');
        $mapelId     = $request->get('mapel_id');
        $bulan       = sprintf('%02d', $request->get('bulan', date('m')));
        $tahun       = $request->get('tahun', date('Y'));

        // Ambil data untuk dropdown filter
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }
        $kelases = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        $mapelQuery = Mapel::query();
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $mapelQuery->where('sekolah_id', $sekolahId);
        }
        $mapels = $mapelQuery->orderBy('nama_mapel', 'asc')->get();

        $siswaList = collect();
        $rekapData = [];
        $jumlahPertemuan = 0;

        if ($kelasId && $mapelId) {
            // Ambil daftar siswa
            $siswaQuery = Siswa::where('kelas_id', $kelasId);
            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $siswaQuery->where(function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)->orWhereNull('sekolah_id');
                });
            }
            $siswaList = $siswaQuery->get()->sortBy(function ($siswa) {
                return $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '';
            })->values();

            // Ambil semua data absensi mapel pada bulan & tahun tersebut
            $queryAbsensi = AbsensiMapel::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan);

            if ($sekolahId && Schema::hasColumn('absensi_mapels', 'sekolah_id')) {
                $queryAbsensi->where('sekolah_id', $sekolahId);
            }

            $absensiRecords = $queryAbsensi->get();

            // Hitung total pertemuan unik yang tercatat di bulan ini
            $jumlahPertemuan = $absensiRecords->unique('pertemuan_ke')->count();

            $absensiGrouped = $absensiRecords->groupBy('siswa_id');

            foreach ($siswaList as $siswa) {
                $absSiswa = $absensiGrouped->get($siswa->id, collect());

                $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;

                foreach ($absSiswa as $abs) {
                    $st = strtoupper(trim($abs->status ?? ''));
                    if (in_array($st, ['H', 'HADIR'])) $hadir++;
                    elseif (in_array($st, ['I', 'IZIN'])) $izin++;
                    elseif (in_array($st, ['S', 'SAKIT'])) $sakit++;
                    elseif (in_array($st, ['A', 'ALPA', 'ALPHA'])) $alpa++;
                }

                $rekapData[$siswa->id] = [
                    'hadir' => $hadir,
                    'izin'  => $izin,
                    'sakit' => $sakit,
                    'alpa'  => $alpa,
                    'total' => $hadir + $izin + $sakit + $alpa,
                ];
            }
        }

        return view('guru.absensi_mapel.rekap', compact(
            'kelases', 'mapels', 'kelasId', 'mapelId', 'bulan', 'tahun', 'siswaList', 'rekapData', 'jumlahPertemuan'
        ));
    }

    /**
     * Cetak rekap absensi mata pelajaran (Print / PDF view)
     */
    public function cetakRekap(Request $request)
    {
        $sekolahId   = $this->getSekolahId();
        $kelasId     = $request->get('kelas_id');
        $mapelId     = $request->get('mapel_id');
        $bulan       = sprintf('%02d', $request->get('bulan', date('m')));
        $tahun       = $request->get('tahun', date('Y'));

        $kelas = Kelas::find($kelasId);
        $mapel = Mapel::find($mapelId);

        if (!$kelas || !$mapel) {
            return redirect()->back()->with('error', 'Silakan pilih Kelas dan Mata Pelajaran terlebih dahulu.');
        }

        // Ambil Siswa
        $siswaQuery = Siswa::where('kelas_id', $kelasId);
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where(function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)->orWhereNull('sekolah_id');
            });
        }
        $siswaList = $siswaQuery->get()->sortBy(function ($siswa) {
            return $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '';
        })->values();

        // Ambil Absensi
        $queryAbsensi = AbsensiMapel::where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($sekolahId && Schema::hasColumn('absensi_mapels', 'sekolah_id')) {
            $queryAbsensi->where('sekolah_id', $sekolahId);
        }

        $absensiRecords = $queryAbsensi->get();
        $absensiGrouped = $absensiRecords->groupBy('siswa_id');

        $rekapData = [];
        foreach ($siswaList as $siswa) {
            $absSiswa = $absensiGrouped->get($siswa->id, collect());
            $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;

            foreach ($absSiswa as $abs) {
                $st = strtoupper(trim($abs->status ?? ''));
                if (in_array($st, ['H', 'HADIR'])) $hadir++;
                elseif (in_array($st, ['I', 'IZIN'])) $izin++;
                elseif (in_array($st, ['S', 'SAKIT'])) $sakit++;
                elseif (in_array($st, ['A', 'ALPA', 'ALPHA'])) $alpa++;
            }

            $rekapData[$siswa->id] = [
                'hadir' => $hadir,
                'izin'  => $izin,
                'sakit' => $sakit,
                'alpa'  => $alpa,
                'total' => $hadir + $izin + $sakit + $alpa,
            ];
        }

        $profilSekolah = class_exists('\App\Models\ProfilSekolah') ? \App\Models\ProfilSekolah::first() : null;
        $user = Auth::user();
        $guru = $user?->guru;

        return view('guru.absensi_mapel.rekap_cetak', compact(
            'kelas', 'mapel', 'siswaList', 'bulan', 'tahun', 'rekapData', 'profilSekolah', 'guru'
        ));
    }
}