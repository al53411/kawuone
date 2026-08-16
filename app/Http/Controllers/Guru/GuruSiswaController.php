<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class GuruSiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa berdasarkan filter sekolah dan hak akses guru.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Siswa::with('kelas');

        // 1. Filter berdasarkan Sekolah
        $this->applySekolahFilter($query, $user);

        // 2. Jika bukan Superadmin/Admin, filter kelas berdasarkan hak akses Guru
        if (!$this->isSuperOrAdmin($user)) {
            $kelasDiampu = $this->getKelasDiampuByUser($user);

            // Jika mengampu kelas tertentu (sebagai Wali Kelas atau Guru Mapel)
            if ($kelasDiampu !== null && $kelasDiampu->isNotEmpty()) {
                $query->whereIn('kelas_id', $kelasDiampu);
            } 
            // Fallback: Jika belum di-assign ke kelas manapun, tampilkan seluruh siswa di sekolahnya
            else if ($user?->sekolah_id && Schema::hasColumn('siswas', 'sekolah_id')) {
                $query->where('sekolah_id', $user->sekolah_id);
            }
        }

        $siswas = $query->latest()->get();

        return view('guru.siswa.index', compact('siswas'));
    }

    /**
     * Menampilkan detail siswa jika sesuai dengan hak aksesnya.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $query = Siswa::with('kelas');

        // 1. Filter berdasarkan Sekolah
        $this->applySekolahFilter($query, $user);

        // 2. Batasi Akses jika bukan Admin
        if (!$this->isSuperOrAdmin($user)) {
            $kelasDiampu = $this->getKelasDiampuByUser($user);

            if ($kelasDiampu !== null && $kelasDiampu->isNotEmpty()) {
                $query->whereIn('kelas_id', $kelasDiampu);
            }
        }

        $siswa = $query->findOrFail($id);

        return view('guru.siswa.show', compact('siswa'));
    }

    /* =========================================================================
     * HELPER METHODS
     * ========================================================================= */

    /**
     * Cek apakah role user adalah superadmin atau admin
     */
    private function isSuperOrAdmin($user): bool
    {
        return in_array($user?->role, ['superadmin', 'admin', 'admin_sekolah']);
    }

    /**
     * Menerapkan filter sekolah_id jika kolom tersedia di tabel siswas
     */
    private function applySekolahFilter($query, $user): void
    {
        $sekolahId = $user?->sekolah_id ?? $user?->guru?->sekolah_id;

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where(function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                  ->orWhereNull('sekolah_id');
            });
        }
    }

    /**
     * Mengembalikan Collection ID Kelas yang diampu oleh Guru
     * Baik dari relasi Wali Kelas MAUPUN tabel pivot `guru_kelas`
     */
    private function getKelasDiampuByUser($user)
    {
        $guru = $user?->guru;

        // Jika tidak ada profil Guru terikat di user
        if (!$guru && class_exists(Guru::class)) {
            $guru = Guru::where('user_id', $user->id)->first();
        }

        if (!$guru) {
            return collect();
        }

        $kelasIds = [];

        // 1. Ambil ID Kelas jika dia Wali Kelas (Direct Relationship)
        $kelasWali = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();
        $kelasIds = array_merge($kelasIds, $kelasWali);

        // 2. Ambil ID Kelas dari Tabel Pivot guru_kelas (Guru Mapel / Pengampu)
        if (method_exists($guru, 'kelas')) {
            $kelasPengampu = $guru->kelas()->pluck('kelas.id')->toArray();
            $kelasIds = array_merge($kelasIds, $kelasPengampu);
        }

        // Hapus duplikasi ID Kelas
        $kelasIds = array_unique(array_filter($kelasIds));

        return collect($kelasIds);
    }
}