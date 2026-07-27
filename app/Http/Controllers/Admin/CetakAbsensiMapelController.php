<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiMapel;
use Illuminate\Http\Request;

class CetakAbsensiMapelController extends Controller
{
    public function index(Request $request)
    {
        $daftar_kelas = Kelas::all();
        
        // Ambil filter dari request
        $kelas_id = $request->get('kelas_id');
        $mapel = $request->get('mapel');
        $bulan = $request->get('bulan', date('Y-m')); // Default bulan sekarang

        $siswas = [];
        $tanggal_list = [];
        $rekap_absen = [];

        if ($kelas_id && $mapel) {
            // 1. Ambil seluruh siswa di kelas terpilih
            $siswas = Siswa::where('kelas_id', $kelas_id)->orderBy('nama_lengkap', 'asc')->get();

            // 2. Ambil daftar tanggal unik di mana mapel tersebut diajarkan pada bulan terpilih
            $tanggal_list = AbsensiMapel::where('kelas_id', $kelas_id)
                ->where('mapel', $mapel)
                ->where('tanggal', 'like', $bulan . '%')
                ->orderBy('tanggal', 'asc')
                ->pluck('tanggal')
                ->unique()
                ->toArray();

            // 3. Ambil data absensi untuk dicocokkan ke matriks tabel cetak
            $absensi = AbsensiMapel::where('kelas_id', $kelas_id)
                ->where('mapel', $mapel)
                ->where('tanggal', 'like', $bulan . '%')
                ->get();

            // Format ke array asosiatif: [siswa_id][tanggal] => status
            foreach ($absensi as $absen) {
                $rekap_absen[$absen->siswa_id][$absen->tanggal] = $absen->status;
            }
        }

        $kelas_aktif = Kelas::find($kelas_id);

        return view('admin.absensi.cetak_mapel', compact(
            'daftar_kelas',
            'siswas',
            'tanggal_list',
            'rekap_absen',
            'kelas_aktif',
            'mapel',
            'bulan'
        ));
    }
}