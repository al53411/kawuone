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
     * Menampilkan daftar siswa untuk guru.
     */
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $guru = $user?->guru;
        $sekolahId = $user?->sekolah_id ?? $guru?->sekolah_id;

        $allKelasIds = [];

        if ($guru) {
            // 1. Ambil ID kelas jika dia Wali Kelas
            if (Schema::hasColumn('kelas', 'guru_id')) {
                $kelasWaliIds = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();
                $allKelasIds = array_merge($allKelasIds, $kelasWaliIds);
            }

            // 2. Ambil ID kelas dari tabel guru_kelas (Pivot) jika method relasi 'kelas' ada di Model Guru
            if (method_exists($guru, 'kelas')) {
                try {
                    $kelasPengampuIds = $guru->kelas()->pluck('kelas.id')->toArray();
                    $allKelasIds = array_merge($allKelasIds, $kelasPengampuIds);
                } catch (\Exception $e) {
                    // Abaikan jika tabel pivot belum disiapin
                }
            }

            $allKelasIds = array_unique(array_filter($allKelasIds));
        }

        $query = Siswa::with('kelas');

        // Jika guru mengampu kelas tertentu -> Tampilkan siswa di kelas-kelas tersebut
        if (!empty($allKelasIds)) {
            $query->whereIn('kelas_id', $allKelasIds);
        } 
        // Fallback jika belum terikat kelas / $allKelasIds kosong -> Tampilkan semua siswa di sekolah tersebut
        else if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where(function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                  ->orWhereNull('sekolah_id');
            });
        }

        $siswas = $query->latest()->get();

        // FALLBACK TERAKHIR: Jika hasil query masih kosong sama sekali, tarik semua data siswa yang ada
        if ($siswas->isEmpty()) {
            $siswas = Siswa::with('kelas')->latest()->get();
        }

        return view('guru.siswa.index', compact('siswas'));
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

        // Fleksibilitas nama kolom (nama_siswa vs nama_lengkap)
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