<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JurnalGuru; 
use Illuminate\Support\Facades\Auth; // <-- INI YANG KURANG (PENTING)
use Carbon\Carbon;

class JurnalApiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input JSON dari Android
        $request->validate([
            'tanggal'    => 'required|date',
            'kelas_id'   => 'required|exists:kelas,id', // Diperketat agar id kelas harus valid di database
            'mapel'      => 'required|string',
            'jam_ke'     => 'required|string',
            'materi'     => 'required|string',
            'kegiatan'   => 'required|string',
            'keterangan' => 'nullable|string',
        ], [
            'tanggal.required'  => 'Tanggal mengajar wajib diisi.',
            'kelas_id.required' => 'Kelas ID wajib diisi.',
            'kelas_id.exists'   => 'Kelas yang dipilih tidak valid.',
            'mapel.required'    => 'Mata pelajaran wajib diisi.',
            'jam_ke.required'   => 'Jam ke- wajib diisi.',
            'materi.required'   => 'Materi pembelajaran wajib diisi.',
            'kegiatan.required' => 'Kegiatan pembelajaran wajib diisi.',
        ]);

        try {
            // 2. Hitung nama hari otomatis dari tanggal
            Carbon::setLocale('id');
            $hari = Carbon::parse($request->tanggal)->translatedFormat('l');

            // 3. Simpan ke Database
            $jurnal = JurnalGuru::create([
                'guru_id'         => Auth::id(), // Mengambil ID guru yang sedang login via token/session API
                'kelas_id'        => $request->kelas_id,
                'hari'            => $hari,
                'tanggal'         => $request->tanggal,
                'jam_ke'          => $request->jam_ke,
                'mapel'           => $request->mapel,
                'materi'          => $request->materi,
                'kegiatan'        => $request->kegiatan,
                'keterangan'      => $request->keterangan,
                'status_validasi' => 'Pending',
            ]);

            // 4. Balasan JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Jurnal mengajar berhasil disimpan!',
                'data'    => $jurnal
            ], 200);

        } catch (\Exception $e) {
            // 5. Balasan JSON error/gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()
            ], 500);
        }
    }
}