<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiMapel;
use App\Models\Sekolah; // 1. Import Model Sekolah / ProfilSekolah
use Illuminate\Http\Request;

class CetakAbsensiMapelController extends Controller
{
    public function index(Request $request)
    {
        $daftar_kelas = Kelas::all();
        
        $kelas_id = $request->get('kelas_id');
        $mapel = $request->get('mapel');
        $bulan = $request->get('bulan', date('Y-m'));

        $siswas = [];
        $tanggal_list = [];
        $rekap_absen = [];

        if ($kelas_id && $mapel) {
            $siswas = Siswa::where('kelas_id', $kelas_id)->orderBy('nama_lengkap', 'asc')->get();

            $tanggal_list = AbsensiMapel::where('kelas_id', $kelas_id)
                ->where('mapel', $mapel)
                ->where('tanggal', 'like', $bulan . '%')
                ->orderBy('tanggal', 'asc')
                ->pluck('tanggal')
                ->unique()
                ->toArray();

            $absensi = AbsensiMapel::where('kelas_id', $kelas_id)
                ->where('mapel', $mapel)
                ->where('tanggal', 'like', $bulan . '%')
                ->get();

            foreach ($absensi as $absen) {
                $rekap_absen[$absen->siswa_id][$absen->tanggal] = $absen->status;
            }
        }

        $kelas_aktif = Kelas::find($kelas_id);

        // 2. Ambil data profil sekolah (termasuk nama_kepsek & nip_kepsek)
        $sekolah = Sekolah::first(); 

        return view('admin.absensi.cetak_mapel', compact(
            'daftar_kelas',
            'siswas',
            'tanggal_list',
            'rekap_absen',
            'kelas_aktif',
            'mapel',
            'bulan',
            'sekolah' // 3. Kirim ke View
        ));
    }
}