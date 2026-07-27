<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalGuru; // 👈 Menggunakan Model JurnalGuru yang benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. Cek Jurnal Hari Ini menggunakan JurnalGuru
        $jurnalHariIni = JurnalGuru::where('guru_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        // 2. Ambil Riwayat/Aktivitas Jurnal Terbaru
        $aktivitasTerbaru = JurnalGuru::with('kelas')
            ->where('guru_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Variabel opsional
        $presensiHariIni = null;
        $totalSoal = 0;

        return view('guru.dashboard', compact(
            'user',
            'jurnalHariIni',
            'presensiHariIni',
            'totalSoal',
            'aktivitasTerbaru'
        ));
    }
}