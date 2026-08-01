<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
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
            'npsn'          => 'required|numeric|unique:sekolahs,npsn',
            'nama_sekolah'  => 'required|string|max:255',
            'jenjang'       => 'required|in:SD,SMP,SMA,SMK',
            'status'        => 'required|in:Negeri,Swasta',
            'alamat'        => 'required|string',
            'desa_kelurahan'=> 'nullable|string|max:100',
            'kecamatan'     => 'nullable|string|max:100',
            'kabupaten_kota'=> 'nullable|string|max:100',
            'provinsi'      => 'nullable|string|max:100',
            'nama_kepsek'   => 'nullable|string|max:255',
            'nip_kepsek'    => 'nullable|string|max:50',
            'telepon'       => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
        ], [
            'npsn.required'      => 'NPSN wajib diisi.',
            'npsn.unique'        => 'NPSN sudah terdaftar di sistem.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'jenjang.required'   => 'Pilih jenjang sekolah.',
            'status.required'    => 'Pilih status sekolah.',
            'alamat.required'    => 'Alamat sekolah wajib diisi.',
        ]);

        Sekolah::create($validatedData);

        return redirect()->back()->with('success', 'Data sekolah berhasil ditambahkan!');
    }
}