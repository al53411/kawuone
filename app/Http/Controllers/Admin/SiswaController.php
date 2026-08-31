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

        return $user->sekolah_id ?? $user->guru?->sekolah_id;
    }

    /**
     * Menampilkan daftar siswa (Menyediakan Filter Kelas & Pencarian Nama/NISN)
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $sekolahId = $this->getSekolahId();

        // 1. Ambil daftar kelas untuk dropdown filter di Blade
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where('sekolah_id', $sekolahId);
        }
        $listKelas = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        // 2. Query Utama Siswa
        $query = Siswa::with('kelas');

        // LOGIKA PENYARINGAN ROLE GURU VS ADMIN
        if ($user && ($user->isGuru() || $user->role === 'guru' || $user->guru)) {
            $guru = $user->guru;
            $allKelasIds = [];

            if ($guru) {
                $kelasWaliIds = Kelas::where('guru_id', $guru->id)->pluck('id')->toArray();
                $kelasPengampuIds = method_exists($guru, 'kelas') 
                    ? $guru->kelas()->pluck('kelas.id')->toArray() 
                    : [];

                $allKelasIds = array_unique(array_merge($kelasWaliIds, $kelasPengampuIds));
            }

            if (!empty($allKelasIds)) {
                $query->whereIn('kelas_id', $allKelasIds);
            } else if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $query->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }
        } else {
            // Role Admin / Superadmin
            if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
                $query->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                });
            }
        }

        // 3. APPLY FILTER: Dropdown Pilih Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // 4. APPLY FILTER: Input Pencarian (Nama Siswa / NISN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Mendukung pencarian kolom 'nama_siswa' maupun 'nama_lengkap' jika ada fallback
                if (Schema::hasColumn('siswas', 'nama_siswa')) {
                    $q->where('nama_siswa', 'like', "%{$search}%");
                } else {
                    $q->where('nama_lengkap', 'like', "%{$search}%");
                }

                $q->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswas = $query->latest()->get();

        // FALLBACK: Jika Guru tidak di-assign kelas spesifik dan hasil pencarian/filter awal kosong
        if ($siswas->isEmpty() && !$request->filled('search') && !$request->filled('kelas_id') && $user && ($user->isGuru() || $user->role === 'guru' || $user->guru) && $sekolahId) {
            $siswas = Siswa::with('kelas')
                ->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                      ->orWhereNull('sekolah_id');
                })
                ->latest()
                ->get();
        }

        return view('admin.siswa.index', compact('siswas', 'listKelas'));
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
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => 'required|string|max:20|unique:siswas,nisn',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nama_siswa.required'   => 'Nama siswa wajib diisi!',
            'nisn.unique'           => 'NISN sudah terdaftar dalam sistem!',
            'nisn.required'         => 'NISN wajib diisi!',
            'kelas_id.required'     => 'Kelas wajib dipilih!',
            'jenis_kelamin.required'=> 'Jenis kelamin wajib dipilih!',
        ]);

        $data = [
            'nama_siswa'    => $validated['nama_siswa'],
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
            'nama_siswa'    => 'required|string|max:255',
            'nisn'          => ['required', 'string', 'max:20', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat'        => 'nullable|string',
        ], [
            'nama_siswa.required'   => 'Nama siswa wajib diisi!',
            'nisn.unique'           => 'NISN sudah digunakan oleh siswa lain!',
            'nisn.required'         => 'NISN wajib diisi!',
            'kelas_id.required'     => 'Kelas wajib dipilih!',
            'jenis_kelamin.required'=> 'Jenis kelamin wajib dipilih!',
        ]);

        $siswa->update([
            'nama_siswa'    => $validated['nama_siswa'],
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