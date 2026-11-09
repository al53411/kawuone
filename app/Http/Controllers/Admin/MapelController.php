<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    /**
     * Helper privat untuk menentukan ID Sekolah
     */
    private function getSekolahId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) return null;

        return $user->sekolah_id ?? $user->guru?->sekolah_id;
    }

    public function index()
    {
        $sekolahId = $this->getSekolahId();

        // 1. Ambil data Mapel beserta relasi Kelases
        $mapelQuery = Mapel::with('kelases');
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $mapelQuery->where(function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                  ->orWhereNull('sekolah_id');
            });
        }
        $mapels = $mapelQuery->latest()->get();

        // 2. Ambil data Kelas untuk pilihan opsi/checkbox
        $kelasQuery = Kelas::query();
        if ($sekolahId && Schema::hasColumn('kelas', 'sekolah_id')) {
            $kelasQuery->where(function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId)
                  ->orWhereNull('sekolah_id');
            });
        }
        $kelases = $kelasQuery->orderBy('nama_kelas', 'asc')->get();

        return view('admin.mapel.index', compact('mapels', 'kelases'));
    }

    public function store(Request $request)
    {
        $sekolahId = $this->getSekolahId();

        $request->validate([
            'nama_mapel' => [
                'required',
                'string',
                'max:255',
                // Validasi Unik: Nama mapel tidak boleh sama dalam satu sekolah yang sama (Aman untuk null)
                Rule::unique('mapels')->where(function ($query) use ($sekolahId) {
                    return $sekolahId 
                        ? $query->where('sekolah_id', $sekolahId) 
                        : $query->whereNull('sekolah_id');
                }),
            ],
            'kategori'   => 'nullable|string|in:kbm,rutin,ekstra', // Opsi jika tabel mapels punya kolom kategori
            'kelas_ids'  => 'nullable|array',
            'kelas_ids.*'=> 'exists:kelas,id',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mapel.unique'   => 'Mata pelajaran dengan nama tersebut sudah terdaftar di sekolah ini.',
        ]);

        // ---------------------------------------------------------
        // LOGIKA GENERATE KODE MAPEL OTOMATIS (Contoh: MPL-001)
        // ---------------------------------------------------------
        $latestMapel = Mapel::when($sekolahId, function($q) use ($sekolahId) {
                                return $q->where('sekolah_id', $sekolahId);
                            }, function($q) {
                                return $q->whereNull('sekolah_id');
                            })
                            ->where('kode_mapel', 'like', 'MPL-%')
                            ->orderBy('id', 'desc')
                            ->first();

        if ($latestMapel) {
            $lastNumber = (int) substr($latestMapel->kode_mapel, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $kodeOtomatis = 'MPL-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Data payload yang akan disimpan
        $payload = [
            'sekolah_id' => $sekolahId,
            'kode_mapel' => $kodeOtomatis,
            'nama_mapel' => $request->nama_mapel,
        ];

        // Jika di database ada kolom 'kategori' dan di-request
        if ($request->has('kategori') && Schema::hasColumn('mapels', 'kategori')) {
            $payload['kategori'] = $request->kategori;
        }

        // Simpan Master Mapel
        $mapel = Mapel::create($payload);

        // Sync ke Tabel Pivot kelas_mapel
        if ($request->has('kelas_ids')) {
            $mapel->kelases()->sync($request->kelas_ids);
        }

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan dengan kode: ' . $kodeOtomatis);
    }

    public function update(Request $request, string $id)
    {
        $sekolahId = $this->getSekolahId();

        $request->validate([
            'nama_mapel' => [
                'required',
                'string',
                'max:255',
                // Validasi Unik saat Edit: Abaikan ID mapel yang sedang diedit
                Rule::unique('mapels')->where(function ($query) use ($sekolahId) {
                    return $sekolahId 
                        ? $query->where('sekolah_id', $sekolahId) 
                        : $query->whereNull('sekolah_id');
                })->ignore($id),
            ],
            'kategori'   => 'nullable|string|in:kbm,rutin,ekstra',
            'kelas_ids'  => 'nullable|array',
            'kelas_ids.*'=> 'exists:kelas,id',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mapel.unique'   => 'Mata pelajaran dengan nama tersebut sudah terdaftar di sekolah ini.',
        ]);

        $mapel = Mapel::findOrFail($id);

        $payload = [
            'nama_mapel' => $request->nama_mapel,
        ];

        if ($request->has('kategori') && Schema::hasColumn('mapels', 'kategori')) {
            $payload['kategori'] = $request->kategori;
        }

        // Update Master Mapel
        $mapel->update($payload);

        // Sync Relasi Kelas
        if ($request->has('kelas_ids')) {
            $mapel->kelases()->sync($request->kelas_ids);
        } else {
            $mapel->kelases()->detach();
        }

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $mapel = Mapel::findOrFail($id);
        
        // Hapus relasi pivot terlebih dahulu
        $mapel->kelases()->detach();
        $mapel->delete();

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}