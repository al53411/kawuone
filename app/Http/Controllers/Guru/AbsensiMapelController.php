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
}