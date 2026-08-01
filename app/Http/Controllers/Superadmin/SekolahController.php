<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    /**
     * Tampilkan semua daftar sekolah (Dashboard Superadmin)
     */
    public function index(Request $request)
    {
        $query = Sekolah::query();

        // Fitur pencarian berdasarkan NPSN atau Nama Sekolah
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }

        // Ambil data dengan pagination agar kompatibel dengan Blade index
        $sekolah = $query->latest()->paginate(10);

        return view('superadmin.sekolah.index', compact('sekolah'));
    }

    /**
     * Tampilkan form tambah sekolah
     */
    public function create()
    {
        return view('superadmin.sekolah.create');
    }

    /**
     * Simpan data sekolah baru ke database
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'npsn'           => 'required|numeric|unique:sekolahs,npsn',
            'nama_sekolah'  => 'required|string|max:255',
            'jenjang'        => 'required|in:SD,SMP,SMA,SMK',
            'status'         => 'required|in:Negeri,Swasta',
            'alamat'         => 'required|string',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi'       => 'nullable|string|max:100',
            'nama_kepsek'    => 'nullable|string|max:255',
            'nip_kepsek'     => 'nullable|string|max:50',
            'telepon'        => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
        ], [
            'npsn.required'         => 'NPSN wajib diisi.',
            'npsn.unique'           => 'NPSN sudah terdaftar di sistem.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'jenjang.required'      => 'Pilih jenjang sekolah.',
            'status.required'       => 'Pilih status sekolah.',
            'alamat.required'       => 'Alamat sekolah wajib diisi.',
        ]);

        Sekolah::create($validatedData);

        return redirect()->route('superadmin.sekolah.index')->with('success', 'Data sekolah berhasil ditambahkan!'); 
    }

    /**
     * Tampilkan detail sekolah
     */
    public function show($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        return view('superadmin.sekolah.show', compact('sekolah'));
    }

    /**
     * Tampilkan form edit sekolah
     */
    public function edit($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        return view('superadmin.sekolah.edit', compact('sekolah'));
    }

    /**
     * Update data sekolah oleh Superadmin
     */
    public function update(Request $request, $id)
    {
        $sekolah = Sekolah::findOrFail($id);

        $validatedData = $request->validate([
            'npsn'           => 'required|numeric|unique:sekolahs,npsn,' . $id,
            'nama_sekolah'  => 'required|string|max:255',
            'jenjang'        => 'required|in:SD,SMP,SMA,SMK',
            'status'         => 'required|in:Negeri,Swasta',
            'alamat'         => 'required|string',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi'       => 'nullable|string|max:100',
            'nama_kepsek'    => 'nullable|string|max:255',
            'nip_kepsek'     => 'nullable|string|max:50',
            'telepon'        => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
        ]);

        $sekolah->update($validatedData);

        return redirect()->route('superadmin.sekolah.index')->with('success', 'Data sekolah berhasil diperbarui!');
    }

    /**
     * Hapus data sekolah
     */
    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->delete();

        return redirect()->route('superadmin.sekolah.index')->with('success', 'Data sekolah berhasil dihapus!');
    }
}