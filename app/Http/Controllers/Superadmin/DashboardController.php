<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah; 
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
        public function index()
{
    $totalSekolah = Sekolah::count();

    // Hitung sekolah yang memiliki user/guru dengan jurnal berstatus 'Disetujui'
    $sekolahValidasi = Sekolah::whereHas('users.jurnals', function ($query) {
        $query->where('status_validasi', 'Disetujui');
    })->count();

    // Hitung persentase
    $persenValidasi = $totalSekolah > 0 
        ? round(($sekolahValidasi / $totalSekolah) * 100, 1) 
        : 0;

    $totalKepsek = Sekolah::whereNotNull('nama_kepsek')->where('nama_kepsek', '!=', '')->count();
    $totalGuru   = User::where('role', 'guru')->count();
    $totalTendik = User::where('role', 'tendik')->count();

    $sekolahs = Sekolah::with('users')->paginate(10);

    return view('superadmin.dashboard', compact(
        'totalSekolah',
        'totalKepsek',
        'totalGuru',
        'totalTendik',
        'persenValidasi',
        'sekolahValidasi',
        'sekolahs'
    ));
}
}