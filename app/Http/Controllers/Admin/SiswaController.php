<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar semua siswa berdasarkan sekolah admin.
     */
    public function index()
    {
        $sekolahId = Auth::user()?->sekolah_id;

        $query = Siswa::with('kelas');

        // Filter berdasarkan sekolah jika kolom sekolah_id ada di tabel siswas
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

        // Ambil kelas sesuai sekolah jika kolom sekolah_id ada di tabel kelas
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

        $rules = [
            'nama_siswa'    => 'required|string|max:255',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ];

        // Aturan unik NISN (terikat sekolah jika ada sekolah_id)
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $rules['nisn'] = 'required|max:20|unique:siswas,nisn,NULL,id,sekolah_id,' . $sekolahId;
        } else {
            $rules['nisn'] = 'required|max:20|unique:siswas,nisn';
        }

        $validated = $request->validate($rules);

        // Suntikkan sekolah_id jika kolom tersedia di tabel siswas
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

        $rules = [
            'nama_siswa'    => 'required|string|max:255',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ];

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $rules['nisn'] = 'required|max:20|unique:siswas,nisn,' . $siswa->id . ',id,sekolah_id,' . $sekolahId;
        } else {
            $rules['nisn'] = 'required|max:20|unique:siswas,nisn,' . $siswa->id;
        }

        $validated = $request->validate($rules);

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