<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    // Menampilkan halaman form data sekolah
    public function index()
    {
        // Mengambil data sekolah baris pertama, jika belum ada nilainya kosong
        $sekolah = Sekolah::first();
        return view('admin.sekolah.index', compact('sekolah'));
    }

    // Menyimpan atau memperbarui data sekolah
    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
        ]);

        // Trik khusus: Mencari ID 1, jika tidak ada maka buat baru. Jika ada langsung di-update.
        Sekolah::updateOrCreate(
            ['id' => 1],
            [
                'npsn' => $request->npsn,
                'nama_sekolah' => $request->nama_sekolah,
                'alamat' => $request->alamat,
                'nama_kepsek' => $request->nama_kepsek,
            ]
        );

        return redirect()->back()->with('success', 'Data Sekolah berhasil diperbarui!');
    }
}