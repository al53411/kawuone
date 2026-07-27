<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah; // Menggunakan model Sekolah
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung ringkasan statistik (Role disesuaikan ke 'kepsek')
        $totalSekolah = Sekolah::count();
        $totalKepsek  = User::where('role', 'kepsek')->count(); 
        $totalGuru    = User::where('role', 'guru')->count();
        $totalTendik  = User::where('role', 'tendik')->count();

        // Ambil daftar sekolah beserta akun Kepala Sekolah terkait
        $sekolahs = Sekolah::with(['users' => function($query) {
            $query->where('role', 'kepsek');
        }])->latest()->paginate(10);

        return view('superadmin.dashboard', compact(
            'totalSekolah', 
            'totalKepsek', 
            'totalGuru', 
            'totalTendik', 
            'sekolahs'
        ));
    }
}