<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MapelController extends Controller
{
    /**
     * Helper privat untuk mendapatkan ID Sekolah dari User/Guru
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
     * Menampilkan daftar mata pelajaran
     */
    public function index(Request $request)
    {
        $sekolahId = $this->getSekolahId();

        $query = Mapel::query();

        // Multi-tenant check per sekolah
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }

        // Filter Pencarian Nama / Kode Mapel
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%");
                if (Schema::hasColumn('mapels', 'kode_mapel')) {
                    $q->orWhere('kode_mapel', 'like', "%{$search}%");
                }
            });
        }

        $mapels = $query->orderBy('nama_mapel', 'asc')->paginate(10);

        return view('guru.mapel.index', compact('mapels'));
    }

    /**
     * Menyimpan mata pelajaran baru
     */
    public function store(Request $request)
    {
        $sekolahId = $this->getSekolahId();

        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'nullable|string|max:50',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi!',
        ]);

        $data = [
            'nama_mapel' => trim($validated['nama_mapel']),
            'kode_mapel' => isset($validated['kode_mapel']) ? trim($validated['kode_mapel']) : null,
        ];

        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $data['sekolah_id'] = $sekolahId;
        }

        Mapel::create($data);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    /**
     * Memperbarui data mata pelajaran
     */
    public function update(Request $request, $id)
    {
        $sekolahId = $this->getSekolahId();

        $query = Mapel::query();
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }
        $mapel = $query->findOrFail($id);

        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'nullable|string|max:50',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi!',
        ]);

        $mapel->update([
            'nama_mapel' => trim($validated['nama_mapel']),
            'kode_mapel' => isset($validated['kode_mapel']) ? trim($validated['kode_mapel']) : null,
        ]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    /**
     * Menghapus data mata pelajaran
     */
    public function destroy($id)
    {
        $sekolahId = $this->getSekolahId();

        $query = Mapel::query();
        if ($sekolahId && Schema::hasColumn('mapels', 'sekolah_id')) {
            $query->where('sekolah_id', $sekolahId);
        }
        $mapel = $query->findOrFail($id);
        $mapel->delete();

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus!');
    }
}