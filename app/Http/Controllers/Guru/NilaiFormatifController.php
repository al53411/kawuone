<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TujuanPembelajaran;
use App\Models\Siswa;
use App\Models\NilaiFormatif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class NilaiFormatifController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id ?? $user->guru?->sekolah_id;

        // 1. Ambil List Kelas & Mapel
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }
        $listKelas = $kelasQuery->orderBy('nama_kelas')->get();
        $listMapel = Mapel::all();

        // 2. Ambil List TP berdasarkan filter mapel_id dan kelas_id
        $listTp = collect();
        if ($request->filled('mapel_id')) {
            $tpQuery = TujuanPembelajaran::where('mapel_id', $request->mapel_id);

            if ($request->filled('kelas_id')) {
                $tpQuery->where(function($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id)
                      ->orWhereNull('kelas_id');
                });
            }

            if ($sekolahId && Schema::hasColumn('tujuan_pembelajarans', 'sekolah_id')) {
                $tpQuery->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }

            $listTp = $tpQuery->get();
        }

        // 3. Ambil data Siswa & Nilai Existing jika filter lengkap
        $listSiswa = collect();
        $nilaiMap = [];

        if ($request->filled(['kelas_id', 'mapel_id', 'tp_id'])) {
            $siswaQuery = Siswa::where('kelas_id', $request->kelas_id);

            if (Schema::hasColumn('siswas', 'nama_siswa')) {
                $siswaQuery->orderBy('nama_siswa', 'asc');
            } else {
                $siswaQuery->orderBy('nama_lengkap', 'asc');
            }

            $listSiswa = $siswaQuery->get();

            // Mapping Nilai yang sudah tersimpan
            $nilaiRecords = NilaiFormatif::where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->where('tp_id', $request->tp_id)
                ->get();

            foreach ($nilaiRecords as $record) {
                $nilaiMap[$record->siswa_id] = $record;
            }
        }

        return view('guru.nilai.formatif', compact(
            'listKelas',
            'listMapel',
            'listTp',
            'listSiswa',
            'nilaiMap'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'  => 'required|exists:kelas,id',
            'mapel_id'  => 'required|exists:mapels,id',
            'tp_id'     => 'required|exists:tujuan_pembelajarans,id',
            'nilai'     => 'nullable|array',
            'nilai.*'   => 'nullable|numeric|min:0|max:100',
            'catatan'   => 'nullable|array',
        ]);

        $sekolahId = Auth::user()->sekolah_id ?? Auth::user()->guru?->sekolah_id;

        if ($request->has('nilai') && is_array($request->nilai)) {
            foreach ($request->nilai as $siswaId => $skor) {
                if ($skor !== null && $skor !== '') {
                    NilaiFormatif::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'tp_id'    => $request->tp_id,
                        ],
                        [
                            'sekolah_id' => $sekolahId,
                            'kelas_id'   => $request->kelas_id,
                            'mapel_id'   => $request->mapel_id,
                            'nilai'      => $skor,
                            'catatan'    => $request->catatan[$siswaId] ?? null,
                        ]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Nilai Formatif berhasil disimpan!');
    }

    /**
     * Endpoint AJAX: Mengambil daftar TP berdasarkan Mapel ID & Kelas ID.
     */
    public function getTpByMapel(Request $request, $mapel_id)
    {
        try {
            $user = Auth::user();
            $sekolahId = $user->sekolah_id ?? $user->guru?->sekolah_id;
            $kelasId = $request->query('kelas_id');

            $tpQuery = TujuanPembelajaran::where('mapel_id', $mapel_id);

            if ($kelasId) {
                $tpQuery->where(function($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId)->orWhereNull('kelas_id');
                });
            }

            if ($sekolahId && Schema::hasColumn('tujuan_pembelajarans', 'sekolah_id')) {
                $tpQuery->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)->orWhereNull('sekolah_id');
                });
            }

            $tps = $tpQuery->get(['id', 'kode_tp', 'deskripsi_tp']);

            return response()->json($tps);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function rekap(Request $request)
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id ?? $user->guru?->sekolah_id;

        // 1. Ambil List Kelas & Mapel
        $listKelas = Kelas::where('sekolah_id', $sekolahId)->orderBy('nama_kelas')->get();
        $listMapel = Mapel::all();

        $listSiswa = collect();
        $listTp = collect();
        $matrixNilai = [];

        // 2. Jika Filter Diisi
        if ($request->filled(['kelas_id', 'mapel_id'])) {
            // Ambil Siswa
            $listSiswa = Siswa::where('kelas_id', $request->kelas_id)
                ->orderBy(Schema::hasColumn('siswas', 'nama_siswa') ? 'nama_siswa' : 'nama_lengkap', 'asc')
                ->get();

            // Ambil Seluruh TP untuk Mapel & Kelas ini
            $listTp = TujuanPembelajaran::where('mapel_id', $request->mapel_id)
                ->where(function($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id)->orWhereNull('kelas_id');
                })
                ->get();

            // Ambil Seluruh Nilai Formatif
            $nilaiRecords = NilaiFormatif::where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->get();

            // Menyusun Matrix Nilai [siswa_id][tp_id] = record
            foreach ($nilaiRecords as $record) {
                $matrixNilai[$record->siswa_id][$record->tp_id] = $record;
            }
        }

        return view('guru.nilai.rekap_formatif', compact(
            'listKelas',
            'listMapel',
            'listSiswa',
            'listTp',
            'matrixNilai'
        ));
    }
}