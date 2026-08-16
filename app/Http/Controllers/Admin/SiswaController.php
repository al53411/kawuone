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
     * Helper privat untuk mendapatkan ID Sekolah dari User (Admin / Guru)
     */
    private function getSekolahId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Ambil dari user->sekolah_id (Admin) atau user->guru->sekolah_id (Guru)
        return $user->sekolah_id ?? $user->guru?->sekolah_id;
    }

    /**
     * Menampilkan daftar siswa (Otomatis menyesuaikan Admin vs Guru)
     */
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $sekolahId = $this->getSekolahId();

        $query = Siswa::with('kelas');

        // 1. JIKA YANG LOGIN ADALAH GURU
        if ($user && ($user->isGuru() || $user->role === 'guru' || $user->guru)) {
            $guru = $user->guru;

            $allKelasIds = [];

            if ($guru) {
                // Ambil ID Kelas tempat guru jadi Wali Kelas
                $kelasWaliIds = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();

                // Ambil ID Kelas dari relasi pivot (guru_kelas)
                $kelasPengampuIds = method_exists($guru, 'kelas') 
                    ? $guru->kelas()->pluck('kelas.id')->toArray() 
                    : [];

                $allKelasIds = array_unique(array_merge($kelasWaliIds, $kelasPengampuIds));
            }

            if (!empty($allKelasIds)) {
                // Filter siswa berdasarkan kelas yang diampu guru
                $query->whereIn('kelas_id', $allKelasIds);
            } else if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                // Fallback 1: Jika guru belum di-assign kelas
                $query->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }

            $siswas = $query->latest()->get();

            // FALLBACK 2: Jika setelah difilter kelas data siswas masih KOSONG (misal miskonfigurasi ID kelas),
            // Tampilkan seluruh siswa di sekolah tersebut agar guru tetap bisa input/melihat data.
            if ($siswas->isEmpty() && $sekolahId) {
                $siswas = Siswa::with('kelas')
                    ->where(function($q) use ($sekolahId) {
                        $q->where('sekolah_id', $sekolahId)
                          ->orWhereNull('sekolah_id');
                    })
                    ->latest()
                    ->get();
            }
        } 
        // 2. JIKA ADMIN / SUPERADMIN
        else {
            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $query->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }
            $siswas = $query->latest()->get();
        }

        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * Menampilkan form untuk menambah siswa baru.
     */
    public function create()
    {
        $sekolahId = $this->getSekolahId();

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
        $sekolahId = $this->getSekolahId();

        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nisn'          => 'required|string|max:20|unique:siswas,nisn',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nama_lengkap.required' => 'Nama lengkap siswa wajib diisi!',
            'nisn.unique'           => 'NISN sudah terdaftar dalam sistem!',
            'nisn.required'         => 'NISN wajib diisi!',
            'kelas_id.required'     => 'Kelas wajib dipilih!',
            'jenis_kelamin.required'=> 'Jenis kelamin wajib dipilih!',
        ]);

        $data = [
            'nama_lengkap'  => $validated['nama_lengkap'],
            'nisn'          => $validated['nisn'],
            'kelas_id'      => $validated['kelas_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat'        => $validated['alamat'] ?? null,
        ];

        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $data['sekolah_id'] = $sekolahId;
        }

        Siswa::create($data);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail spesifik seorang siswa.
     */
    public function show(string $id)
    {
        $sekolahId = $this->getSekolahId();

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
        $sekolahId = $this->getSekolahId();

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
        $sekolahId = $this->getSekolahId();

        $siswaQuery = Siswa::query();
        if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where('sekolah_id', $sekolahId);
        }
        $siswa = $siswaQuery->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nisn'          => ['required', 'string', 'max:20', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nama_lengkap.required' => 'Nama lengkap siswa wajib diisi!',
            'nisn.unique'           => 'NISN sudah digunakan oleh siswa lain!',
            'nisn.required'         => 'NISN wajib diisi!',
            'kelas_id.required'     => 'Kelas wajib dipilih!',
            'jenis_kelamin.required'=> 'Jenis kelamin wajib dipilih!',
        ]);

        $siswa->update([
            'nama_lengkap'  => $validated['nama_lengkap'],
            'nisn'          => $validated['nisn'],
            'kelas_id'      => $validated['kelas_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat'        => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa dari database.
     */
    public function destroy(string $id)
    {
        $sekolahId = $this->getSekolahId();

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