<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage (TAMBAH DATA).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Hanya field yang ada di form Blade)
        $validatedData = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => 'required|numeric|unique:siswas,nisn', // Menangkap NISN ganda sebelum masuk ke DB
            'kelas_id'      => 'required',
            'jenis_kelamin' => 'required',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'   => 'NISN sudah terdaftar dalam sistem!',
            'nisn.numeric'  => 'NISN harus berupa angka!',
            'required'      => 'Field ini wajib diisi!',
        ]);

        // 2. Set sekolah_id secara otomatis dari user/guru yang sedang login
        $validatedData['sekolah_id'] = Auth::user()->sekolah_id ?? 1;

        // 3. Simpan Data ke Database
        Siswa::create($validatedData);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage (EDIT DATA).
     */
    public function update(Request $request, string $id)
    {
        // 1. Validasi Input Edit Data
        $validatedData = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => 'required|numeric|unique:siswas,nisn,' . $id, 
            'kelas_id'      => 'required',
            'jenis_kelamin' => 'required',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'   => 'NISN sudah digunakan oleh siswa lain!',
            'nisn.numeric'  => 'NISN harus berupa angka!',
            'required'      => 'Field ini wajib diisi!',
        ]);

        // 2. Update Data ke Database
        $siswa = Siswa::findOrFail($id);
        
        // Tetapkan sekolah_id jika belum ada
        if (!$siswa->sekolah_id) {
            $validatedData['sekolah_id'] = Auth::user()->sekolah_id ?? 1;
        }

        $siswa->update($validatedData);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }
}