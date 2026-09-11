<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapaianPembelajaran;
use App\Models\Mapel;
use App\Models\Kelas; // 1. Import Model Kelas
use Illuminate\Http\Request;

class CapaianPembelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = CapaianPembelajaran::with('mapel');

        // Filter berdasarkan Mapel & Fase
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }
        if ($request->filled('fase')) {
            $query->where('fase', $request->fase);
        }

        // Menggunakan pagination agar tampilan tabel tetap ringan
        $cps = $query->latest()->paginate(15)->withQueryString();
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        
        // 2. Ambil data Kelas dari database
        $kelass = Kelas::orderBy('nama_kelas', 'asc')->get();

        // 3. Passing $kelass ke view
        return view('admin.cp.index', compact('cps', 'mapels', 'kelass'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mapel_id'     => 'required|exists:mapels,id',
            'fase'         => 'required|in:A,B,C,D,E,F',
            'elemen'       => 'required|string|max:150',
            'deskripsi_cp' => 'required|string',
        ]);

        CapaianPembelajaran::create($validated);

        return redirect()->back()->with('success', 'Capaian Pembelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, CapaianPembelajaran $capaianPembelajaran)
    {
        $validated = $request->validate([
            'mapel_id'     => 'required|exists:mapels,id',
            'fase'         => 'required|in:A,B,C,D,E,F',
            'elemen'       => 'required|string|max:150',
            'deskripsi_cp' => 'required|string',
        ]);

        $capaianPembelajaran->update($validated);

        return redirect()->back()->with('success', 'Capaian Pembelajaran berhasil diperbarui!');
    }

    public function destroy(CapaianPembelajaran $capaianPembelajaran)
    {
        $capaianPembelajaran->delete();

        return redirect()->back()->with('success', 'Capaian Pembelajaran berhasil dihapus!');
    }
}