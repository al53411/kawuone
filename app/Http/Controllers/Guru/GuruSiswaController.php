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

        // 3. Ambil ID Guru (Cek apakah user terhubung ke Model Guru atau langsung)
        $guruId = $user->guru_id;
        if (!$guruId && class_exists(Guru::class)) {
            $guruId = Guru::where('user_id', $user->id)->value('id');
        }
        $guruId = $guruId ?? $user->id;

        // 4. Cari kelas yang diampu/di-wali-kan oleh guru ini
        // (Mendukung role 'guru', 'guru_kelas', maupun 'wali_kelas')
        $kelasDiampu = Kelas::where('guru_id', $guruId)->pluck('id');

        // Jika guru ini terdaftar sebagai wali kelas / guru kelas
        if ($kelasDiampu->isNotEmpty()) {
            // Filter siswa HANYA dari kelas yang diampu
            $query->whereIn('kelas_id', $kelasDiampu);
        } else {
            // JIKA BUKAN WALI KELAS ATAU BELUM DIPLOT KELAS:
            // Cek jika di tabel users ada kolom 'kelas_id' langsung
            if (!empty($user->kelas_id)) {
                $query->where('kelas_id', $user->kelas_id);
            } 
            // Jika role-nya murni 'guru' biasa dan BUKAN guru_mapel,
            // kembalikan data kosong agar tidak melihat siswa kelas lain
            elseif (in_array($user->role, ['guru', 'guru_kelas', 'wali_kelas'])) {
                $query->whereRaw('1 = 0'); // Trik me-return query kosong dengan aman
            }
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

        // Proteksi: Pastikan hanya bisa lihat siswa di sekolah yang sama
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        // Jika bukan superadmin, proteksi berdasarkan kelas diampu
        if ($user->role !== 'superadmin') {
            $guruId = $user->guru_id;
            if (!$guruId && class_exists(Guru::class)) {
                $guruId = Guru::where('user_id', $user->id)->value('id');
            }
            $guruId = $guruId ?? $user->id;

            $kelasDiampu = Kelas::where('guru_id', $guruId)->pluck('id');

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