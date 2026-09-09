<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class TahunAjaranController extends Controller
{
    public function index()
    {
        $sekolahId = Auth::user()->sekolah_id;
        $tahunAjaran = TahunAjaran::where('sekolah_id', $sekolahId)->latest()->get();

        return view('admin.tahun_ajaran.index', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        TahunAjaran::create([
            'sekolah_id' => Auth::user()->sekolah_id,
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'is_aktif' => DB::raw('false'), // Memaksa nilai boolean false di PostgreSQL
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan!');
    }

    public function setAktif($id)
    {
        $sekolahId = Auth::user()->sekolah_id;

        // Reset semua tahun ajaran di sekolah ini menjadi false
        TahunAjaran::where('sekolah_id', $sekolahId)->update(['is_aktif' => DB::raw('false')]);

        // Set tahun ajaran yang dipilih menjadi true
        $ta = TahunAjaran::where('sekolah_id', $sekolahId)->findOrFail($id);
        $ta->update(['is_aktif' => DB::raw('true')]);

        return redirect()->back()->with('success', 'Tahun Ajaran aktif berhasil diperbarui!');
    }
}