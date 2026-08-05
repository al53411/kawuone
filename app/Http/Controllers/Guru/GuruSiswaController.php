<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
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

        // 1. Wajib filter berdasarkan sekolah guru yang login
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        // 2. Filter khusus jika yang login adalah Guru Kelas / Wali Kelas
        // Asumsi: role/tipe di user ditandai dengan 'guru_kelas' atau 'wali_kelas'
        if ($user->role === 'guru_kelas' || $user->role === 'wali_kelas') {
            
            // Cari ID kelas yang diampu oleh wali kelas ini
            $kelasDiampu = Kelas::where('guru_id', $user->guru_id ?? $user->id)
                ->pluck('id');

            // Filter siswa hanya dari kelas diampu
            $query->whereIn('kelas_id', $kelasDiampu);
        } 
        // Jika $user->role === 'guru_mapel', tidak difilter kelasnya (bisa lihat semua siswa di sekolah tersebut)

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

        // Proteksi Tambahan: Jika Guru Kelas, pastikan siswa tersebut memang murid di kelasnya
        if ($user->role === 'guru_kelas' || $user->role === 'wali_kelas') {
            $kelasDiampu = Kelas::where('guru_id', $user->guru_id ?? $user->id)
                ->pluck('id');

            $query->whereIn('kelas_id', $kelasDiampu);
        }

        $siswa = $query->findOrFail($id);

        return view('guru.siswa.show', compact('siswa'));
    }
}