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

        // 2. Jika Wali Kelas (bukan Guru Mapel & bukan Admin), filter siswa berdasarkan kelas diampunya
        if (!$this->isSuperOrAdmin($user)) {
            $kelasDiampu = $this->getKelasDiampuByUser($user);

            if ($kelasDiampu !== null) {
                // Jika mengampu kelas tertentu, filter siswanya
                if ($kelasDiampu->isNotEmpty()) {
                    $query->whereIn('kelas_id', $kelasDiampu);
                } else {
                    // Wali Kelas tetapi belum disetting kelasnya -> kosongkan data
                    $query->whereRaw('1 = 0');
                }
            }
            // Catatan: Jika $kelasDiampu === null, artinya dia Guru Mapel -> tampilkan semua siswa di sekolah tersebut
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

        // 2. Batasi Akses jika bukan Admin / Guru Mapel
        if (!$this->isSuperOrAdmin($user)) {
            $kelasDiampu = $this->getKelasDiampuByUser($user);

            if ($kelasDiampu !== null) {
                if ($kelasDiampu->isNotEmpty()) {
                    $query->whereIn('kelas_id', $kelasDiampu);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        $siswa = $query->findOrFail($id);

        return view('guru.siswa.show', compact('siswa'));
    }

    /* =========================================================================
     * HELPER METHODS (Private Functions untuk Menghindari Duplikasi Kode)
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
        if ($user?->sekolah_id && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $user->sekolah_id);
        }
    }

    /**
     * Mengembalikan Collection ID Kelas yang diampu jika Wali Kelas,
     * atau mengembalikan NULL jika user adalah Guru Mapel (menandakan akses ke semua kelas).
     */
    private function getKelasDiampuByUser($user)
    {
        $userId = $user->id;
        $userName = $user->name;
        $guruId = $user->guru_id;
        $guruName = null;
        $guruData = null;

        // Ambil Profil Guru jika model Guru ada
        if (class_exists(Guru::class)) {
            $guruData = Guru::where('user_id', $userId)->orWhere('id', $guruId)->first();
            if ($guruData) {
                $guruId = $guruData->id;
                $guruName = $guruData->nama_guru ?? $guruData->nama ?? $guruData->nama_lengkap ?? null;
            }
        }

        // Cek Status Guru Mapel
        $isGuruMapel = false;
        if ($guruData) {
            $jabatanLower = strtolower($guruData->jabatan ?? '');
            $jenisGuruLower = strtolower($guruData->jenis_guru ?? '');

            $isGuruMapel = str_contains($jabatanLower, 'mapel') ||
                           str_contains($jabatanLower, 'mata pelajaran') ||
                           str_contains($jenisGuruLower, 'mapel') ||
                           !empty($guruData->mata_pelajaran);
        }

        // Jika Guru Mapel -> kembalikan null (bebas lihat semua kelas)
        if ($isGuruMapel) {
            return null;
        }

        // Jika Wali Kelas -> cari kelas yang diampu
        $possibleIdentifiers = array_filter([$userId, $guruId, $userName, $guruName]);
        $tableName = (new Kelas)->getTable();
        $kelasQuery = Kelas::query();
        $hasRelation = false;

        if (Schema::hasColumn($tableName, 'wali_kelas')) {
            $kelasQuery->whereIn('wali_kelas', $possibleIdentifiers);
            $hasRelation = true;
        } elseif (Schema::hasColumn($tableName, 'guru_id')) {
            $kelasQuery->whereIn('guru_id', $possibleIdentifiers);
            $hasRelation = true;
        } elseif (Schema::hasColumn($tableName, 'wali_kelas_id')) {
            $kelasQuery->whereIn('wali_kelas_id', $possibleIdentifiers);
            $hasRelation = true;
        } elseif (Schema::hasColumn($tableName, 'user_id')) {
            $kelasQuery->whereIn('user_id', $possibleIdentifiers);
            $hasRelation = true;
        }

        $kelasDiampu = $hasRelation ? $kelasQuery->pluck('id') : collect();

        // Fallback ke kelas_id langsung di user jika relasi kelas tidak ketemu
        if ($kelasDiampu->isEmpty() && !empty($user->kelas_id)) {
            return collect([$user->kelas_id]);
        }

        return $kelasDiampu;
    }
}