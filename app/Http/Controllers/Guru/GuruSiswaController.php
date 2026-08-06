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
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $sekolahId = $user?->sekolah_id;

        $query = Siswa::with('kelas');

        // 1. Filter Wajib: Berdasarkan Sekolah dari User yang Login
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        // 2. Jika Superadmin, Tampilkan Semua Data
        if ($user->role === 'superadmin') {
            $siswas = $query->latest()->get();
            return view('guru.siswa.index', compact('siswas'));
        }

        // 3. Tentukan ID Guru
        $guruId = $user->guru_id;
        if (!$guruId && class_exists(Guru::class)) {
            $guruId = Guru::where('user_id', $user->id)->value('id');
        }
        $guruId = $guruId ?? $user->id;

        // 4. Deteksi Nama Tabel Kelas yang Aktif (Mencegah Mismatch 'kelas' vs 'kelases')
        $tableName = (new Kelas)->getTable(); // Otomatis mendapatkan nama tabel dari Model Eloquent

        // 5. Cari Kelas yang Diampu
        $kelasQuery = Kelas::query();
        $hasRelation = false;

        if (Schema::hasColumn($tableName, 'guru_id')) {
            $kelasQuery->where('guru_id', $guruId);
            $hasRelation = true;
        } elseif (Schema::hasColumn($tableName, 'user_id')) {
            $kelasQuery->where('user_id', $user->id);
            $hasRelation = true;
        } elseif (Schema::hasColumn($tableName, 'wali_kelas_id')) {
            $kelasQuery->where('wali_kelas_id', $guruId);
            $hasRelation = true;
        }

        $kelasDiampu = $hasRelation ? $kelasQuery->pluck('id') : collect();

        // 6. Filter Siswa Berdasarkan Kelas (dengan Fallback yang Aman)
        if ($kelasDiampu->isNotEmpty()) {
            // Wali Kelas: Tampilkan hanya siswa di kelas yang diampu
            $query->whereIn('kelas_id', $kelasDiampu);
        } elseif (!empty($user->kelas_id)) {
            // Jika kelas terikat langsung di user
            $query->where('kelas_id', $user->kelas_id);
        } else {
            // FALLBACK: Jika Guru Mapel / Belum Diplot Wali Kelas
            // Sistem tetap menampilkan data siswa di sekolah tersebut tanpa filter kelas_id
        }

        $siswas = $query->latest()->get();

        return view('guru.siswa.index', compact('siswas'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $sekolahId = $user?->sekolah_id;

        $query = Siswa::with('kelas');

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        if ($user->role !== 'superadmin') {
            $guruId = $user->guru_id;
            if (!$guruId && class_exists(Guru::class)) {
                $guruId = Guru::where('user_id', $user->id)->value('id');
            }
            $guruId = $guruId ?? $user->id;

            $tableName = (new Kelas)->getTable();
            $kelasQuery = Kelas::query();
            $hasRelation = false;

            if (Schema::hasColumn($tableName, 'guru_id')) {
                $kelasQuery->where('guru_id', $guruId);
                $hasRelation = true;
            } elseif (Schema::hasColumn($tableName, 'user_id')) {
                $kelasQuery->where('user_id', $user->id);
                $hasRelation = true;
            } elseif (Schema::hasColumn($tableName, 'wali_kelas_id')) {
                $kelasQuery->where('wali_kelas_id', $guruId);
                $hasRelation = true;
            }

            $kelasDiampu = $hasRelation ? $kelasQuery->pluck('id') : collect();

            if ($kelasDiampu->isNotEmpty()) {
                $query->whereIn('kelas_id', $kelasDiampu);
            } elseif (!empty($user->kelas_id)) {
                $query->where('kelas_id', $user->kelas_id);
            }
        }

        $siswa = $query->findOrFail($id);

        return view('guru.siswa.show', compact('siswa'));
    }
}