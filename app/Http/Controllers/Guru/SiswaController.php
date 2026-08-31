<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa KHUSUS kelas milik guru yang login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $guru = $user?->guru;
        $sekolahId = $user?->sekolah_id ?? $guru?->sekolah_id;

        // Ambil daftar kelas untuk di-pass ke dropdown Blade
        $listKelas = Kelas::where('sekolah_id', $sekolahId)->get();

        $query = Siswa::with('kelas');

        // 1. Filter Berdasarkan Kelas yang dipilih
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        } 
        // Jika tidak pilih kelas & bukan guru mapel, kunci ke kelas wali
        else if ($guru && $guru->jabatan !== 'Guru Mapel') {
            $kelasWaliIds = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();
            if (!empty($kelasWaliIds)) {
                $query->whereIn('kelas_id', $kelasWaliIds);
            }
        }

        // 2. Filter Berdasarkan Pencarian (Nama / NISN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswas = $query->latest()->get();

        return view('guru.siswa.index', compact('siswas', 'listKelas'));
    }

    /**
     * Store data siswa baru dari modal/form guru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => 'required|numeric|unique:siswas,nisn',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'   => 'NISN sudah terdaftar dalam sistem!',
            'nisn.numeric'  => 'NISN harus berupa angka!',
            'required'      => 'Field ini wajib diisi!',
        ]);

        if (Schema::hasColumn('siswas', 'nama_lengkap')) {
            $validatedData['nama_lengkap'] = $validatedData['nama_siswa'];
        }

        $sekolahId = $user?->sekolah_id ?? $user?->guru?->sekolah_id;
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $validatedData['sekolah_id'] = $sekolahId;
        }

        Siswa::create($validatedData);

        return redirect()->route('guru.siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Update data siswa.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => 'required|numeric|unique:siswas,nisn,' . $id, 
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'   => 'NISN sudah digunakan oleh siswa lain!',
            'nisn.numeric'  => 'NISN harus berupa angka!',
            'required'      => 'Field ini wajib diisi!',
        ]);

        $siswa = Siswa::findOrFail($id);

        if (Schema::hasColumn('siswas', 'nama_lengkap')) {
            $validatedData['nama_lengkap'] = $validatedData['nama_siswa'];
        }

        $sekolahId = $user?->sekolah_id ?? $user?->guru?->sekolah_id;
        if (!$siswa->sekolah_id && $sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $validatedData['sekolah_id'] = $sekolahId;
        }

        $siswa->update($validatedData);

        return redirect()->route('guru.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Hapus data siswa.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }
}