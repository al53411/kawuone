<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlurTujuanPembelajaran;
use App\Models\TujuanPembelajaran;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlurTujuanPembelajaranController extends Controller
{
    /**
     * Menampilkan daftar Alur Tujuan Pembelajaran (ATP)
     */
    public function index(Request $request)
    {
        $sekolahId = Auth::user()->sekolah_id ?? null;

        $query = AlurTujuanPembelajaran::with(['tujuanPembelajaran', 'mapel', 'kelas'])
            ->when($sekolahId, function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            });

        // Filter dinas/user jika memilih Mapel/Kelas tertentu
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $atps = $query->orderBy('semester', 'asc')
                      ->orderBy('urutan', 'asc')
                      ->get();

        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();
        $tps = TujuanPembelajaran::when($sekolahId, function ($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId);
                })->orderBy('kode_tp', 'asc')->get();

        return view('admin.atp.index', compact('atps', 'mapels', 'kelases', 'tps'));
    }

    /**
     * Menyimpan data ATP baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tujuan_pembelajaran_id' => 'required|exists:tujuan_pembelajarans,id',
            'mapel_id'               => 'required|exists:mapels,id',
            'kelas_id'               => 'nullable|exists:kelas,id',
            'kode_atp'               => 'required|string|max:50',
            'semester'               => 'required|in:1,2',
            'urutan'                 => 'required|integer|min:1',
            'alokasi_jp'             => 'nullable|integer|min:1',
            'rincian_materi'         => 'required|string',
        ], [
            'tujuan_pembelajaran_id.required' => 'Tujuan Pembelajaran (TP) wajib dipilih.',
            'mapel_id.required'               => 'Mata pelajaran wajib dipilih.',
            'kode_atp.required'               => 'Kode ATP wajib diisi.',
            'rincian_materi.required'         => 'Rincian alur materi pembelajaran wajib diisi.',
        ]);

        AlurTujuanPembelajaran::create([
            'sekolah_id'             => Auth::user()->sekolah_id ?? null,
            'tujuan_pembelajaran_id' => $request->tujuan_pembelajaran_id,
            'mapel_id'               => $request->mapel_id,
            'kelas_id'               => $request->kelas_id,
            'kode_atp'               => $request->kode_atp,
            'semester'               => $request->semester,
            'urutan'                 => $request->urutan,
            'alokasi_jp'             => $request->alokasi_jp ?? 2,
            'rincian_materi'         => $request->rincian_materi,
        ]);

        return redirect()->back()->with('success', 'Alur Tujuan Pembelajaran (ATP) berhasil ditambahkan!');
    }

    /**
     * Memperbarui data ATP
     */
    public function update(Request $request, $id)
    {
        $atp = AlurTujuanPembelajaran::findOrFail($id);

        $request->validate([
            'tujuan_pembelajaran_id' => 'required|exists:tujuan_pembelajarans,id',
            'mapel_id'               => 'required|exists:mapels,id',
            'kelas_id'               => 'nullable|exists:kelas,id',
            'kode_atp'               => 'required|string|max:50',
            'semester'               => 'required|in:1,2',
            'urutan'                 => 'required|integer|min:1',
            'alokasi_jp'             => 'nullable|integer|min:1',
            'rincian_materi'         => 'required|string',
        ]);

        $atp->update([
            'tujuan_pembelajaran_id' => $request->tujuan_pembelajaran_id,
            'mapel_id'               => $request->mapel_id,
            'kelas_id'               => $request->kelas_id,
            'kode_atp'               => $request->kode_atp,
            'semester'               => $request->semester,
            'urutan'                 => $request->urutan,
            'alokasi_jp'             => $request->alokasi_jp,
            'rincian_materi'         => $request->rincian_materi,
        ]);

        return redirect()->back()->with('success', 'ATP berhasil diperbarui!');
    }

    /**
     * Menghapus data ATP
     */
    public function destroy($id)
    {
        $atp = AlurTujuanPembelajaran::findOrFail($id);
        $atp->delete();

        return redirect()->back()->with('success', 'ATP berhasil dihapus!');
    }

    /**
     * API JSON untuk mengambil ATP berdasarkan TP / Mapel (Dipakai AJAX form Jurnal/Modul)
     */
    public function getByTp(Request $request)
    {
        $query = AlurTujuanPembelajaran::query();

        if (Auth::check() && Auth::user()->sekolah_id) {
            $query->where('sekolah_id', Auth::user()->sekolah_id);
        }

        if ($request->filled('tp_id')) {
            $query->where('tujuan_pembelajaran_id', $request->tp_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $atps = $query->orderBy('urutan', 'asc')->get();

        return response()->json($atps);
    }
}