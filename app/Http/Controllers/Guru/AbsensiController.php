<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\Mapel;
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
     * Menampilkan halaman rekap/input absensi khusus Guru
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
        
        // Ambil ID Guru jika ada relasi ke tabel guru, jika tidak gunakan ID User
        $guruId    = $user?->guru?->id ?? Auth::id();

        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $siswaIds = array_keys($request->absensi);
        $siswas   = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');

        // Gunakan DB Transaction untuk menjamin integritas data saat penyimpan beruntun (looping)
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

                // Kondisi pencarian unik
                $matchConditions = [
                    'siswa_id' => $siswaId,
                    'tanggal'  => $tanggal,
                    'mapel'    => $mapel,
                ];

                if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                    $matchConditions['sekolah_id'] = $sekolahId;
                }

                // Data yang akan di-update / insert
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
}