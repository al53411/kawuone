<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah total data secara real-time dari database
        $total_siswa = Siswa::count();
        $total_guru = Guru::count();
        $total_kelas = Kelas::count();

        return view('admin.dashboard.index', compact('total_siswa', 'total_guru', 'total_kelas'));
    }
}