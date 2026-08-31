<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalGuru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil user dari Auth, atau gunakan fallback dummy user role 'guru' jika Auth kosong (Bypass Mode)
        $user = Auth::user() ?? User::where('role', 'guru')->first() ?? User::first();

        // Antisipasi jika database benar-benar kosong
        if (!$user) {
            return response('Error Testing: Tidak ada data user/guru di database. Silakan jalankan seeder.', 404);
        }

        // Paksa login user dummy ke session Auth agar Blade View (Auth::user()) tidak crash
        Auth::login($user);

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