<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AbsensiController extends Controller
{
    /**
     * Helper privat untuk mendapatkan ID Sekolah dari User/Guru
     */
    private function getSekolahId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $user->sekolah_id ?? $user->guru?->sekolah_id;
    }

    /**
     * Menampilkan halaman input absensi khusus Guru
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $sekolahId = $this->getSekolahId();

        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $kelasId = $request->get('kelas_id');
        $mapel   = trim($request->get('mapel', ''));

        // 1. Ambil Data Mapel (Mata Pelajaran) untuk dropdown di View
        $mapelQuery = Mapel::query();
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $mapelQuery->where('sekolah_id', $sekolahId);
        }
        $mapels = $mapelQuery->orderBy('nama_mapel', 'asc')->get();

        // 2. Filter Kelas yang Diampu / Menjadi Wali bagi Guru Tersebut
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }

        $guru = $user?->guru;
        if ($guru) {
            $kelasWaliIds = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();
            $kelasPengampuIds = method_exists($guru, 'kelas') 
                ? $guru->kelas()->pluck('kelas.id')->toArray() 
                : [];

            $allowedKelasIds = array_unique(array_merge($kelasWaliIds, $kelasPengampuIds));

            if (!empty($allowedKelasIds)) {
                $kelasQuery->whereIn('id', $allowedKelasIds);
            }
        }

        $kelases = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        $siswas          = collect();
        $absensiExisting = [];

        // 3. Ambil Siswa dan Data Absensi jika Kelas dipilih
        if ($kelasId) {
            $siswaQuery = Siswa::where('kelas_id', $kelasId);

            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $siswaQuery->where(function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }

            if ($request->filled('search')) {
                $search = trim($request->search);
                $siswaQuery->where(function ($q) use ($search) {
                    if (Schema::hasColumn('siswas', 'nama_siswa')) {
                        $q->where('nama_siswa', 'like', "%{$search}%");
                    } else {
                        $q->where('nama_lengkap', 'like', "%{$search}%");
                    }
                    $q->orWhere('nisn', 'like', "%{$search}%");
                });
            }

            $siswas = $siswaQuery->get()
                ->sortBy(function ($siswa) {
                    return $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '';
                })
                ->values();

            // Query Data Absensi
            $queryAbsensi = Absensi::where('tanggal', $tanggal)
                ->where('kelas_id', $kelasId);

            if ($mapel !== '') {
                $queryAbsensi->where('mapel', $mapel);
            } else {
                $queryAbsensi->where(function ($q) {
                    $q->whereNull('mapel')->orWhere('mapel', '');
                });
            }

            if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                $queryAbsensi->where('sekolah_id', $sekolahId);
            }

            $absensiExisting = $queryAbsensi->get()->keyBy('siswa_id');
        }

        return view('guru.absensi.index', compact('kelases', 'siswas', 'tanggal', 'absensiExisting', 'mapels'));
    }

    /**
     * Menyimpan data absensi siswa oleh Guru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'absensi'  => 'required|array',
            'kelas_id' => 'required|exists:kelas,id',
        ], [
            'tanggal.required'  => 'Tanggal absensi wajib diisi!',
            'absensi.required'  => 'Data absensi tidak boleh kosong!',
            'kelas_id.required' => 'Kelas wajib dipilih!',
        ]);

        /** @var \App\Models\User|null $user */
        $user      = Auth::user();
        $sekolahId = $this->getSekolahId();
        $tanggal   = $request->tanggal;
        $mapel     = !empty($request->mapel) ? trim($request->mapel) : null;
        
        $guruId    = $user?->guru?->id ?? Auth::id();

        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $siswaIds = array_keys($request->absensi);
        $siswas   = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');

        DB::transaction(function () use ($request, $siswas, $kelas, $tanggal, $mapel, $sekolahId, $guruId) {
            foreach ($request->absensi as $siswaId => $data) {
                $status = $data['status'] ?? null;
                if (!$status) {
                    continue;
                }

                $siswa = $siswas->get($siswaId);
                $finalKelasId = $kelas->id ?? $siswa?->kelas_id;

                if (!$finalKelasId) {
                    continue;
                }

                $matchConditions = [
                    'siswa_id' => $siswaId,
                    'tanggal'  => $tanggal,
                    'mapel'    => $mapel,
                ];

                if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                    $matchConditions['sekolah_id'] = $sekolahId;
                }

                $updateData = [
                    'kelas_id'   => $finalKelasId,
                    'guru_id'    => $guruId,
                    'status'     => $status,
                    'keterangan' => $data['keterangan'] ?? null,
                ];

                if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                    $updateData['sekolah_id'] = $sekolahId;
                }

                Absensi::updateOrCreate($matchConditions, $updateData);
            }
        });

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan!');
    }

    /**
     * Menampilkan halaman rekap absensi bulanan
     */
    public function rekap(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $sekolahId = $this->getSekolahId();

        $bulan = sprintf('%02d', $request->get('bulan', date('m')));
        $tahun = $request->get('tahun', date('Y'));
        $kelasId = $request->get('kelas_id');

        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }

        $guru = $user?->guru;
        if ($guru) {
            $kelasWaliIds = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();
            $kelasPengampuIds = method_exists($guru, 'kelas') 
                ? $guru->kelas()->pluck('kelas.id')->toArray() 
                : [];

            $allowedKelasIds = array_unique(array_merge($kelasWaliIds, $kelasPengampuIds));
            if (!empty($allowedKelasIds)) {
                $kelasQuery->whereIn('id', $allowedKelasIds);
            }
        }

        $daftarKelas = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        if (!$kelasId && $daftarKelas->isNotEmpty()) {
            $kelasId = $daftarKelas->first()->id;
        }

        $siswaList = collect();
        $rekapData = [];
        $jumlahHari = Carbon::createFromDate((int)$tahun, (int)$bulan)->daysInMonth;

        if ($kelasId) {
            $siswaQuery = Siswa::where('kelas_id', $kelasId);
            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $siswaQuery->where(function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)->orWhereNull('sekolah_id');
                });
            }

            $siswaList = $siswaQuery->get()->sortBy(function ($siswa) {
                return $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '';
            })->values();

            $queryAbsensi = Absensi::where('kelas_id', $kelasId)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan);

            if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                $queryAbsensi->where('sekolah_id', $sekolahId);
            }

            $absensiData = $queryAbsensi->get()->groupBy('siswa_id');

            foreach ($siswaList as $siswa) {
                $absensiSiswa = $absensiData->get($siswa->id, collect());

                $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;

                foreach ($absensiSiswa as $abs) {
                    $st = strtoupper(trim($abs->status ?? ''));
                    if (in_array($st, ['H', 'HADIR'])) {
                        $hadir++;
                    } elseif (in_array($st, ['I', 'IZIN'])) {
                        $izin++;
                    } elseif (in_array($st, ['S', 'SAKIT'])) {
                        $sakit++;
                    } elseif (in_array($st, ['A', 'ALPA', 'ALPHA'])) {
                        $alpa++;
                    }
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

        return view('guru.absensi.rekap', compact(
            'daftarKelas',
            'kelasId',
            'bulan',
            'tahun',
            'jumlahHari',
            'siswaList',
            'rekapData'
        ));
    }

    /**
     * Cetak rekapitulasi absensi bulanan (PDF / Printable View)
     */
    public function cetakRekap(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $sekolahId = $this->getSekolahId();

        $bulan = sprintf('%02d', $request->get('bulan', date('m')));
        $tahun = $request->get('tahun', date('Y'));
        $kelasId = $request->get('kelas_id');

        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }

        // 1. Ambil Siswa
        $siswaQuery = Siswa::where('kelas_id', $kelasId);
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where(function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)->orWhereNull('sekolah_id');
            });
        }
        $siswaList = $siswaQuery->get()->sortBy(function ($siswa) {
            return $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? '';
        })->values();

        // 2. Ambil Absensi
        $queryAbsensi = Absensi::where('kelas_id', $kelasId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
            $queryAbsensi->where('sekolah_id', $sekolahId);
        }

        $absensiRecords = $queryAbsensi->get();

        // 3. Pemetaan Matriks Harian & Hitung Rekap
        $matrixHarian = [];
        $rekapData = [];

        foreach ($absensiRecords as $abs) {
            $tglKey = Carbon::parse($abs->tanggal)->format('Y-m-d');
            
            $statusRaw = strtoupper(trim($abs->status ?? ''));
            $kode = 'H';
            if (in_array($statusRaw, ['I', 'IZIN'])) $kode = 'I';
            elseif (in_array($statusRaw, ['S', 'SAKIT'])) $kode = 'S';
            elseif (in_array($statusRaw, ['A', 'ALPA', 'ALPHA'])) $kode = 'A';

            $matrixHarian[$abs->siswa_id][$tglKey] = $kode;
        }

        foreach ($siswaList as $siswa) {
            $absensiSiswa = $absensiRecords->where('siswa_id', $siswa->id);

            $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;

            foreach ($absensiSiswa as $abs) {
                $st = strtoupper(trim($abs->status ?? ''));
                if (in_array($st, ['H', 'HADIR'])) {
                    $hadir++;
                } elseif (in_array($st, ['I', 'IZIN'])) {
                    $izin++;
                } elseif (in_array($st, ['S', 'SAKIT'])) {
                    $sakit++;
                } elseif (in_array($st, ['A', 'ALPA', 'ALPHA'])) {
                    $alpa++;
                }
            }

            $rekapData[$siswa->id] = [
                'hadir' => $hadir,
                'izin'  => $izin,
                'sakit' => $sakit,
                'alpa'  => $alpa,
                'total' => $hadir + $izin + $sakit + $alpa,
            ];
        }

        // 4. Ambil Profil Sekolah
        $profilSekolah = class_exists('\App\Models\ProfilSekolah') 
            ? \App\Models\ProfilSekolah::first() 
            : null;

        // 5. Logika Pencarian Kepala Sekolah
        $kepalaSekolah = User::whereIn('role', ['kepala_sekolah', 'kepsek', 'ks'])->first();

        if (!$kepalaSekolah && Schema::hasColumn('users', 'jabatan')) {
            $kepalaSekolah = User::where('jabatan', 'Kepala Sekolah')->first();
        }

        if (!$kepalaSekolah && $profilSekolah) {
            $kepalaSekolah = (object)[
                'nama' => $profilSekolah->nama_kepsek ?? $profilSekolah->kepala_sekolah ?? null,
                'nip'  => $profilSekolah->nip_kepsek ?? $profilSekolah->nip_kepala_sekolah ?? null,
            ];
        }

        $guru = $user?->guru;

        return view('guru.absensi.rekap_cetak', compact(
            'kelas',
            'kelasId',
            'siswaList',
            'bulan',
            'tahun',
            'matrixHarian',
            'rekapData',
            'profilSekolah',
            'kepalaSekolah',
            'guru'
        ));
    }
}