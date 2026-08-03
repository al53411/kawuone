<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id ?? null;

        $siswaQuery = Siswa::query();
        $guruQuery  = Guru::query();
        $kelasQuery = Kelas::query();
        $logQuery   = class_exists(ActivityLog::class) ? ActivityLog::query() : null;

        // Hanya tambahkan filter sekolah_id jika user punya sekolah_id DAN kolomnya memang ada di tabel
        if ($sekolahId) {
            if (Schema::hasColumn('siswas', 'sekolah_id')) {
                $siswaQuery->where('sekolah_id', $sekolahId);
            }
            
            if (Schema::hasColumn('gurus', 'sekolah_id')) {
                $guruQuery->where('sekolah_id', $sekolahId);
            }

            if (Schema::hasColumn('kelas', 'sekolah_id')) {
                $kelasQuery->where('sekolah_id', $sekolahId);
            }

            if ($logQuery && Schema::hasColumn('activity_logs', 'sekolah_id')) {
                $logQuery->where('sekolah_id', $sekolahId);
            }
        }

            return view('admin.dashboard.index', [
            'total_siswa' => $siswaQuery->count(),
            'total_guru'  => $guruQuery->count(),
            'total_kelas' => $kelasQuery->count(),
            'aktivitas'   => $logQuery ? $logQuery->latest()->take(5)->get() : collect(),
        ]);
    }
}