<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $sekolahId = Auth::user()->sekolah_id;

        // 1. Ambil KELAS & GURU khusus sekolah user yang sedang login
        $kelas = Kelas::where('sekolah_id', $sekolahId)->latest()->get();
        $gurus = Guru::where('sekolah_id', $sekolahId)->get();

        return view('admin.kelas.index', compact('kelas', 'gurus'));
    }

    public function store(Request $request)
    {
        $sekolahId = Auth::user()->sekolah_id;

        // 2. Validasi agar 'nama_kelas' unik HANYA di dalam sekolah yang sama
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->where(function ($query) use ($sekolahId) {
                    return $query->where('sekolah_id', $sekolahId);
                }),
            ],
            'wali_kelas' => 'required|string',
        ]);

        // 3. Simpan data kelas beserta sekolah_id
        Kelas::create([
            'sekolah_id' => $sekolahId,
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
        ]);

        return redirect()->back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    // 4. Update Data Kelas
    public function update(Request $request, $id)
    {
        $sekolahId = Auth::user()->sekolah_id;

        // Pastikan kelas yang diedit milik sekolah admin yang sedang login
        $kelas = Kelas::where('sekolah_id', $sekolahId)->findOrFail($id);

        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')
                    ->where(function ($query) use ($sekolahId) {
                        return $query->where('sekolah_id', $sekolahId);
                    })
                    ->ignore($kelas->id), // Abaikan id kelas ini agar tidak terkena error duplicate saat update
            ],
            'wali_kelas' => 'required|string',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
        ]);

        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui!');
    }

    // 5. Hapus Data Kelas
    public function destroy($id)
    {
        $sekolahId = Auth::user()->sekolah_id;

        // Pastikan kelas yang dihapus milik sekolah admin yang sedang login
        $kelas = Kelas::where('sekolah_id', $sekolahId)->findOrFail($id);
        $kelas->delete();

        return redirect()->back()->with('success', 'Kelas berhasil dihapus!');
    }
}