<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class KelasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sekolahId = $user?->sekolah_id;

        // 1. Ambil Data Kelas
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }
        $kelas = $kelasQuery->latest()->get();

        // 2. Ambil Data Siswa
        $siswaQuery = Siswa::with('kelas');
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where('sekolah_id', $sekolahId);
        }
        $siswas = $siswaQuery->latest()->get();

        // 3. Ambil Data Guru untuk Dropdown Wali Kelas
        $guruQuery = Guru::query();
        if ($sekolahId && Schema::hasColumn('gurus', 'sekolah_id')) {
            $guruQuery->where('sekolah_id', $sekolahId);
        }
        $gurus = $guruQuery->get();

        return view('admin.kelas.index', compact('kelas', 'siswas', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $data = $request->only(['nama_kelas', 'wali_kelas']);

        if ($user?->sekolah_id && Schema::hasColumn('kelas', 'sekolah_id')) {
            $data['sekolah_id'] = $user->sekolah_id;
        }

        Kelas::create($data);

        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $kelas = Kelas::with('siswas')->findOrFail($id);
        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit(string $id)
    {
        $user = Auth::user();
        $sekolahId = $user?->sekolah_id;

        $kelas = Kelas::findOrFail($id);

        $guruQuery = Guru::query();
        if ($sekolahId && Schema::hasColumn('gurus', 'sekolah_id')) {
            $guruQuery->where('sekolah_id', $sekolahId);
        }
        $gurus = $guruQuery->get();

        return view('admin.kelas.edit', compact('kelas', 'gurus'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas' => 'nullable|string|max:255',
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->only(['nama_kelas', 'wali_kelas']));

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui');
    }

    // ✅ METHOD DESTROY UNTUK MEMPROSES HAPUS KELAS
    public function destroy(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->back()->with('success', 'Data kelas berhasil dihapus');
    }
}