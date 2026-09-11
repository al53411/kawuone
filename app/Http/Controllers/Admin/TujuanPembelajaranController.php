<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TujuanPembelajaran;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TujuanPembelajaranController extends Controller
{
    /**
     * Tampilkan daftar Tujuan Pembelajaran
     */
    public function index()
    {
        $sekolahId = Auth::user()->sekolah_id ?? null;

        $tps = TujuanPembelajaran::with(['mapel', 'kelas'])
            ->when($sekolahId, function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            })
            ->latest()
            ->get();

        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.tujuan_pembelajaran.index', compact('tps', 'mapels', 'kelases'));
    }

    /**
     * Simpan TP baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'mapel_id'     => 'required|exists:mapels,id',
            'kelas_id'     => 'nullable|exists:kelas,id',
            'fase'         => 'nullable|string|max:10',
            'elemen'       => 'nullable|string|max:255',
            'kode_tp'      => 'nullable|string|max:50',
            'deskripsi_tp' => 'required|string',
        ], [
            'mapel_id.required'     => 'Mata pelajaran wajib dipilih.',
            'deskripsi_tp.required' => 'Deskripsi Tujuan Pembelajaran wajib diisi.',
        ]);

        TujuanPembelajaran::create([
            'sekolah_id'   => Auth::user()->sekolah_id ?? null,
            'mapel_id'     => $request->mapel_id,
            'kelas_id'     => $request->kelas_id,
            'fase'         => $request->fase,
            'elemen'       => $request->elemen,
            'kode_tp'      => $request->kode_tp,
            'deskripsi_tp' => $request->deskripsi_tp,
        ]);

        return redirect()->back()->with('success', 'Tujuan Pembelajaran berhasil ditambahkan!');
    }

    /**
     * Update TP yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $tp = TujuanPembelajaran::findOrFail($id);

        $request->validate([
            'mapel_id'     => 'required|exists:mapels,id',
            'kelas_id'     => 'nullable|exists:kelas,id',
            'fase'         => 'nullable|string|max:10',
            'elemen'       => 'nullable|string|max:255',
            'kode_tp'      => 'nullable|string|max:50',
            'deskripsi_tp' => 'required|string',
        ]);

        $tp->update([
            'mapel_id'     => $request->mapel_id,
            'kelas_id'     => $request->kelas_id,
            'fase'         => $request->fase,
            'elemen'       => $request->elemen,
            'kode_tp'      => $request->kode_tp,
            'deskripsi_tp' => $request->deskripsi_tp,
        ]);

        return redirect()->back()->with('success', 'Tujuan Pembelajaran berhasil diperbarui!');
    }

    /**
     * Hapus TP
     */
    public function destroy($id)
    {
        $tp = TujuanPembelajaran::findOrFail($id);
        $tp->delete();

        return redirect()->back()->with('success', 'Tujuan Pembelajaran berhasil dihapus!');
    }

    /**
     * API JSON untuk mengambil daftar TP berdasarkan mapel/mapel_id & kelas_id
     * (Fleksibel mendukung ID mapel maupun String nama_mapel dari AJAX Jurnal Guru)
     */
    public function getByMapel(Request $request)
    {
        $query = TujuanPembelajaran::query();

        // Filter Sekolah
        if (Auth::check() && Auth::user()->sekolah_id) {
            $query->where('sekolah_id', Auth::user()->sekolah_id);
        }

        // 1. Filter berdasarkan Mapel (Bisa via mapel_id atau string nama mapel)
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        } elseif ($request->filled('mapel')) {
            $query->whereHas('mapel', function ($q) use ($request) {
                $q->where('nama_mapel', $request->mapel);
            });
        }

        // 2. Filter berdasarkan Kelas (Opsional)
        if ($request->filled('kelas_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id)
                  ->orWhereNull('kelas_id'); // Tetap ambil TP yang diset universal/semua kelas
            });
        }

        $tps = $query->orderBy('kode_tp', 'asc')->get();

        return response()->json($tps);
    }
}