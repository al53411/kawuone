<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
// Tambahkan library Schema & Blueprint di bawah ini
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // FIX OTOMATIS: Membuat tabel absensis jika belum terbentuk di SQLite
        if (!Schema::hasTable('absensis')) {
            Schema::create('absensis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->date('tanggal');
                $table->string('status'); // Hadir, Sakit, Izin, Alfa
                $table->timestamps();
            });
        }

        // Ambil tanggal hari ini atau gunakan tanggal yang dipilih user
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        
        // Membaca seluruh data siswa dari DB
        $siswas = Siswa::with(['kelas'])->get();

        // Ambil data absensi yang sudah tercatat pada tanggal tersebut
        $absensiHariIni = Absensi::where('tanggal', $tanggal)->pluck('status', 'siswa_id')->toArray();

        return view('admin.absensi.index', compact('siswas', 'tanggal', 'absensiHariIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|array',
        ]);

        $tanggal = $request->tanggal;

        // Looping aman menggunakan '?? []' agar tidak memicu error jika input kosong
        foreach ($request->status ?? [] as $siswa_id => $status_kehadiran) {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswa_id,
                    'tanggal' => $tanggal
                ],
                [
                    'status' => $status_kehadiran
                ]
            );
        }

        return redirect()->back()->with('success', 'Data absensi siswa berhasil disimpan!');
    }
}