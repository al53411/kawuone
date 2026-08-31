<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AbsensiController extends Controller
{
    /**
     * Helper privat untuk mendapatkan ID Sekolah dari User
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

    public function index(Request $request)
    {
        $sekolahId = $this->getSekolahId();

        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $kelasId = $request->get('kelas_id');
        $mapel   = $request->get('mapel');
        $search  = $request->get('search');

        // 1. Ambil daftar kelas sesuai sekolah_id
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }
        $kelases = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        $siswas          = collect();
        $absensiExisting = [];

        // 2. Jika kelas dipilih, ambil data siswa dan absensi yang ada
        if ($kelasId) {
            $siswaQuery = Siswa::where('kelas_id', $kelasId);

            // Filter sekolah pada siswa jika ada
            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $siswaQuery->where(function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }

            // Optional Filter Pencarian Nama/NISN
            if ($request->filled('search')) {
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

            // Query Absensi Existing
            $queryAbsensi = Absensi::where('tanggal', $tanggal)
                ->where('kelas_id', $kelasId);

            if ($mapel) {
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

        return view('admin.absensi.index', compact('kelases', 'siswas', 'tanggal', 'absensiExisting'));
    }

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

        $sekolahId = $this->getSekolahId();
        $tanggal   = $request->tanggal;
        $mapel     = $request->mapel;
        $userId    = Auth::id();

        // 1. Validasi Kelas
        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        // 2. Load Data Siswa sekaligus untuk cegah N+1 Query
        $siswaIds = array_keys($request->absensi);
        $siswas   = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');

        foreach ($request->absensi as $siswaId => $data) {
            $status = $data['status'] ?? null;
            if (!$status) {
                continue; // Skip jika status tidak dipilih
            }

            $siswa = $siswas->get($siswaId);
            $finalKelasId = $kelas->id ?? $siswa?->kelas_id;

            if (!$finalKelasId) {
                continue;
            }

            // Kondisi Unik Pencarian
            $matchConditions = [
                'siswa_id' => $siswaId,
                'tanggal'  => $tanggal,
                'mapel'    => $mapel ?? null,
            ];

            if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                $matchConditions['sekolah_id'] = $sekolahId;
            }

            $updateData = [
                'kelas_id'   => $finalKelasId,
                'guru_id'    => $userId,
                'status'     => $status,
                'keterangan' => $data['keterangan'] ?? null,
            ];

            if ($sekolahId && Schema::hasColumn('absensis', 'sekolah_id')) {
                $updateData['sekolah_id'] = $sekolahId;
            }

            Absensi::updateOrCreate($matchConditions, $updateData);
        }

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan!');
    }
}