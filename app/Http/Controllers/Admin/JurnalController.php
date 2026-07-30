<?php

namespace App\Http\Controllers\Guru; // <-- Ubah Admin jadi Guru

use App\Http\Controllers\Controller;
use App\Models\JurnalGuru;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JurnalController extends Controller
{
    /**
     * Menampilkan daftar jurnal milik guru yang sedang login.
     */
    public function index()
    {
        $jurnals = JurnalGuru::with('kelas')
            ->where('guru_id', Auth::id())
            ->latest()
            ->get();

        $kelas = Kelas::all();

        return view('guru.jurnal.index', compact('jurnals', 'kelas'));
    }

    /**
     * Menampilkan form tambah jurnal.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('guru.jurnal.create', compact('kelas'));
    }

    /**
     * Menyimpan jurnal mengajar baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'tanggal'    => 'required|date',
            'jam_ke'     => 'required|string',
            'mapel'      => 'required|string|max:255',
            'materi'     => 'required|string',
            'kegiatan'   => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $namaHari = Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');

        JurnalGuru::create([
            'guru_id'         => Auth::id(),
            'kelas_id'        => $request->kelas_id,
            'hari'            => $namaHari,
            'tanggal'         => $request->tanggal,
            'jam_ke'          => $request->jam_ke,
            'mapel'           => $request->mapel,
            'materi'          => $request->materi,
            'kegiatan'        => $request->kegiatan,
            'keterangan'      => $request->keterangan ?? 'Lancar',
            'status_validasi' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Jurnal mengajar berhasil disimpan dan menunggu validasi Kepala Sekolah.');
    }

    /**
     * Menghapus jurnal mengajar milik guru sendiri.
     */
    public function destroy($id)
    {
        $jurnal = JurnalGuru::where('guru_id', Auth::id())->findOrFail($id);
        $jurnal->delete();

        return redirect()->back()->with('success', 'Jurnal mengajar berhasil dihapus.');
    }
}