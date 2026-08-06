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

        // 1. Filter berdasarkan sekolah guru yang login
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        // 2. Jika Superadmin, izinkan lihat semua siswa
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

        // 4. Cari kelas diampu dengan mengecek nama kolom yang ADA di tabel 'kelas'
        $kelasQuery = Kelas::query();
        
        if (Schema::hasColumn('kelas', 'guru_id')) {
            $kelasQuery->where('guru_id', $guruId);
        } elseif (Schema::hasColumn('kelas', 'user_id')) {
            $kelasQuery->where('user_id', $user->id);
        } elseif (Schema::hasColumn('kelas', 'wali_kelas_id')) {
            $kelasQuery->where('wali_kelas_id', $guruId);
        } else {
            // Jika tidak ada kolom relasi guru di kelas, fallback ke user->kelas_id
            $kelasQuery->whereRaw('1 = 0');
        }

        $kelasDiampu = $kelasQuery->pluck('id');

        // 5. Filter Siswa berdasarkan kelas
        if ($kelasDiampu->isNotEmpty()) {
            $query->whereIn('kelas_id', $kelasDiampu);
        } elseif (!empty($user->kelas_id)) {
            $query->where('kelas_id', $user->kelas_id);
        } else {
            // Jika guru belum diplot ke kelas manapun
            $query->whereRaw('1 = 0');
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

            $kelasQuery = Kelas::query();
            if (Schema::hasColumn('kelas', 'guru_id')) {
                $kelasQuery->where('guru_id', $guruId);
            } elseif (Schema::hasColumn('kelas', 'user_id')) {
                $kelasQuery->where('user_id', $user->id);
            } elseif (Schema::hasColumn('kelas', 'wali_kelas_id')) {
                $kelasQuery->where('wali_kelas_id', $guruId);
            } else {
                $kelasQuery->whereRaw('1 = 0');
            }

            $kelasDiampu = $kelasQuery->pluck('id');

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