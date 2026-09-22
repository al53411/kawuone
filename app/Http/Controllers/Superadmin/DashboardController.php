<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah; 
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\JurnalGuru; // Sesuaikan dengan Model Jurnal mengajar Anda jika ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Sekolah
        $totalSekolah = Sekolah::count();

        // 2. Statistik Validasi Jurnal
        // Menghitung berapa banyak sekolah yang sudah memiliki validasi jurnal/aktivitas jurnal
        $sekolahValidasi = 0;
        
        if (class_exists(JurnalGuru::class) && Schema::hasColumn('jurnals', 'sekolah_id')) {
            $sekolahValidasi = Sekolah::whereHas('jurnals', function ($q) {
                // Sesuaikan kondisi misal status terverifikasi/tervalidasi
                if (Schema::hasColumn('jurnals', 'status')) {
                    $q->whereIn('status', ['valid', 'disetujui', 'terverifikasi']);
                }
            })->count();
        } else {
            // Fallback jika belum ada model Jurnal spesifik
            $sekolahValidasi = Sekolah::whereNotNull('nama_kepsek')->where('nama_kepsek', '!=', '')->count();
        }

        $persenValidasi = $totalSekolah > 0 
            ? round(($sekolahValidasi / $totalSekolah) * 100, 1) 
            : 0;

        // 3. Total Kepala Sekolah
        $totalKepsek = Sekolah::where(function($q) {
            $q->whereNotNull('nama_kepsek')->where('nama_kepsek', '!=', '');
        })->orWhereHas('users', function($q) {
            $q->where('role', 'kepsek');
        })->count();

        // 4. Total Guru
        if (class_exists(Guru::class)) {
            $totalGuru = Guru::count();
        } else {
            $totalGuru = User::where(function ($q) {
                $q->where('role', 'guru')->orWhereHas('guru');
            })->count();
        }

        // 5. Total Tenaga Teknis (Tendik / Staff)
        if (class_exists(Siswa::class)) {
            // Jika ada model Siswa tersendiri
            $totalTendik = User::whereIn('role', ['tendik', 'staff', 'admin_sekolah', 'teknisi'])->count();
        } else {
            // Mengambil dari role tendik/staff/siswa pada tabel users
            $totalTendik = User::whereIn('role', ['tendik', 'staff', 'siswa'])->count();
        }

        // 6. Data Sekolah dengan eager loading relasi users
        $sekolahs = Sekolah::with(['users' => function($q) {
            $q->where('role', 'kepsek');
        }])->latest()->paginate(10);

        return view('superadmin.dashboard', compact(
            'totalSekolah',
            'sekolahValidasi',
            'persenValidasi',
            'totalKepsek',
            'totalGuru',
            'totalTendik',
            'sekolahs'
        ));
    }
}