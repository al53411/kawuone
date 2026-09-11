<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KelasController extends Controller
{
    /**
     * Helper privat untuk menentukan ID Sekolah (Admin / Guru)
     */
    private function getSekolahId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) return null;

        return $user->sekolah_id ?? $user->guru?->sekolah_id;
    }

    /**
     * Helper privat untuk menentukan nama kolom nama guru secara fleksibel
     */
    private function getNamaGuruColumn(): string
    {
        if (Schema::hasColumn('gurus', 'nama_guru')) {
            return 'nama_guru';
        } elseif (Schema::hasColumn('gurus', 'nama_lengkap')) {
            return 'nama_lengkap';
        } elseif (Schema::hasColumn('gurus', 'nama')) {
            return 'nama';
        } elseif (Schema::hasColumn('gurus', 'name')) {
            return 'name';
        }
        
        return 'id';
    }

    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $sekolahId = $this->getSekolahId();
        $kolomNamaGuru = $this->getNamaGuruColumn();

        // 1. Ambil Data Kelas
        $kelasQuery = Kelas::with(['waliKelas', 'siswas', 'gurus']);
        
        if ($user && ($user->isGuru() || $user->role === 'guru')) {
            $guru = $user->guru;
            if ($guru) {
                $kelasQuery->where(function($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                    if (method_exists($guru, 'kelas')) {
                        $kelasIds = $guru->kelas()->pluck('kelas.id')->toArray();
                        $q->orWhereIn('id', $kelasIds);
                    }
                });
            }
        } else {
            if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
                $kelasQuery->where(function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                    ->orWhereNull('sekolah_id');
                });
            }
        }

        $kelas = $kelasQuery->latest()->get();

        // 2. Data Siswa
        $kelasIdsTersedia = $kelas->pluck('id')->toArray();
        $siswaQuery = Siswa::with('kelas');
        if (!empty($kelasIdsTersedia)) {
            $siswaQuery->whereIn('kelas_id', $kelasIdsTersedia);
        } else if ($sekolahId && Schema::hasColumn('siswas', 'sekolah_id')) {
            $siswaQuery->where(function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                ->orWhereNull('sekolah_id');
            });
        }
        $siswas = $siswaQuery->latest()->get();

        // 3. Data Guru
        $guruQuery = Guru::query();
        if ($sekolahId && Schema::hasColumn('gurus', 'sekolah_id')) {
            $guruQuery->where(function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                ->orWhereNull('sekolah_id');
            });
        }
        $gurus = $guruQuery->orderBy($kolomNamaGuru, 'asc')->get();

        return view('admin.kelas.index', compact('kelas', 'siswas', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'guru_id'    => 'nullable|exists:gurus,id',  // Wali Kelas
            'guru_ids'   => 'nullable|array',            // Guru Pengampu (Checkbox)
            'guru_ids.*' => 'exists:gurus,id',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'guru_id.exists'      => 'Guru yang dipilih tidak valid.',
        ]);

        $sekolahId = $this->getSekolahId();
        
        $data = [
            'nama_kelas' => $request->nama_kelas,
            'guru_id'    => $request->guru_id,
        ];

        if (Schema::hasColumn('kelas', 'wali_kelas')) {
            if ($request->filled('guru_id')) {
                $guru = Guru::find($request->guru_id);
                $kolomNamaGuru = $this->getNamaGuruColumn();
                $data['wali_kelas'] = $guru?->{$kolomNamaGuru};
            } else {
                $data['wali_kelas'] = null;
            }
        }

        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $data['sekolah_id'] = $sekolahId;
        }

        // 1. Simpan Data Kelas
        $kelas = Kelas::create($data);

        // 2. Simpan Guru Pengampu (Jika relasi gurus adalah BelongsToMany/Pivot)
        if ($request->has('guru_ids') && method_exists($kelas, 'gurus')) {
            if ($kelas->gurus() instanceof BelongsToMany) {
                $kelas->gurus()->sync($request->guru_ids);
            }
        }

        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $kelas = Kelas::with(['siswas', 'waliKelas', 'gurus'])->findOrFail($id);
        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit(string $id)
    {
        $sekolahId = $this->getSekolahId();
        $kolomNamaGuru = $this->getNamaGuruColumn();

        $kelas = Kelas::with('gurus')->findOrFail($id);

        $guruQuery = Guru::query();
        if ($sekolahId && Schema::hasColumn('gurus', 'sekolah_id')) {
            $guruQuery->where(function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                  ->orWhereNull('sekolah_id');
            });
        }
        $gurus = $guruQuery->orderBy($kolomNamaGuru, 'asc')->get();

        return view('admin.kelas.edit', compact('kelas', 'gurus'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'guru_id'    => 'nullable|exists:gurus,id',
            'guru_ids'   => 'nullable|array',
            'guru_ids.*' => 'exists:gurus,id',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'guru_id.exists'      => 'Guru yang dipilih tidak valid.',
        ]);

        $kelas = Kelas::findOrFail($id);
        
        $data = [
            'nama_kelas' => $request->nama_kelas,
            'guru_id'    => $request->guru_id,
        ];

        if (Schema::hasColumn('kelas', 'wali_kelas')) {
            if ($request->filled('guru_id')) {
                $guru = Guru::find($request->guru_id);
                $kolomNamaGuru = $this->getNamaGuruColumn();
                $data['wali_kelas'] = $guru?->{$kolomNamaGuru};
            } else {
                $data['wali_kelas'] = null;
            }
        }

        // 1. Update Data Kelas
        $kelas->update($data);

        // 2. Sync Guru Pengampu jika tipe relasinya BelongsToMany
        if (method_exists($kelas, 'gurus')) {
            if ($kelas->gurus() instanceof BelongsToMany) {
                if ($request->has('guru_ids')) {
                    $kelas->gurus()->sync($request->guru_ids);
                } else {
                    $kelas->gurus()->detach();
                }
            }
        }

        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->siswas()->count() > 0) {
            return redirect()->back()->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa!');
        }

        // Hapus relasi pivot jika bertipe BelongsToMany
        if (method_exists($kelas, 'gurus')) {
            if ($kelas->gurus() instanceof BelongsToMany) {
                $kelas->gurus()->detach();
            }
        }

        $kelas->delete();

        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }
}