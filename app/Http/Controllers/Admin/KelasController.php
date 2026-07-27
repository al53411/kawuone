<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru; // 1. IMPORT MODEL GURU DI SINI
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        $gurus = Guru::all(); // 2. AMBIL SEMUA DATA GURU DARI DATABASE

        // 3. KIRIM DATA GURUS KE VIEW
        return view('admin.kelas.index', compact('kelas', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'wali_kelas' => 'required|string', // Pastikan input wali_kelas tervalidasi
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas, // Menyimpan nama guru pilihan ke tabel kelas
        ]);

        return redirect()->back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }
}