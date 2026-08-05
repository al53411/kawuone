<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar semua siswa berdasarkan sekolah admin.
     */
    public function index()
    {
        $sekolahId = Auth::user()?->sekolah_id;

        $query = Siswa::with('kelas');

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        $siswas = $query->latest()->get();

        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * Menampilkan form untuk menambah siswa baru.
     */
    public function create()
    {
        $sekolahId = Auth::user()?->sekolah_id;

        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelas = Kelas::where('sekolah_id', $sekolahId)->get();
        } else {
            $kelas = Kelas::all();
        }

        return view('admin.siswa.create', compact('kelas'));
    }

    /**
     * Menyimpan data siswa baru ke database.
     */
    public function store(Request $request)
    {
        $sekolahId = Auth::user()?->sekolah_id;

        // Validasi Sederhana & Pasti Terkunci di Level Laravel
        $validated = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => 'required|string|max:20|unique:siswas,nisn',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'   => 'NISN sudah terdaftar dalam sistem!',
            'nisn.required' => 'NISN wajib diisi!',
            'required'      => 'Field ini wajib diisi!',
        ]);

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $validated['sekolah_id'] = $sekolahId;
        }

        Siswa::create($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail spesifik seorang siswa.
     */
    public function show(string $id)
    {
        $sekolahId = Auth::user()?->sekolah_id;

        $query = Siswa::with('kelas');

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        $siswa = $query->findOrFail($id);

        return view('admin.siswa.show', compact('siswa'));
    }

    /**
     * Menampilkan form untuk mengedit data siswa.
     */
    public function edit(string $id)
    {
        $sekolahId = Auth::user()?->sekolah_id;

        $siswaQuery = Siswa::query();
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where('sekolah_id', $sekolahId);
        }
        $siswa = $siswaQuery->findOrFail($id);

        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelas = Kelas::where('sekolah_id', $sekolahId)->get();
        } else {
            $kelas = Kelas::all();
        }

        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Memperbarui data siswa di database.
     */
    public function update(Request $request, string $id)
    {
        $sekolahId = Auth::user()?->sekolah_id;

        $siswaQuery = Siswa::query();
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where('sekolah_id', $sekolahId);
        }
        $siswa = $siswaQuery->findOrFail($id);

        $validated = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => ['required', 'string', 'max:20', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'   => 'NISN sudah digunakan oleh siswa lain!',
            'nisn.required' => 'NISN wajib diisi!',
            'required'      => 'Field ini wajib diisi!',
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa dari database.
     */
    public function destroy(string $id)
    {
        $sekolahId = Auth::user()?->sekolah_id;

        $siswaQuery = Siswa::query();
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where('sekolah_id', $sekolahId);
        }
        $siswa = $siswaQuery->findOrFail($id);
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}