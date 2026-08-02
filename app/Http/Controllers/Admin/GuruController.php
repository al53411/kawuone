<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Menampilkan daftar data Guru
     */
    public function index()
    {
        $gurus = Guru::with('user')->latest()->get();

        return view('admin.guru.index', compact('gurus'));
    }

    /**
     * Menampilkan form tambah Guru
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Menyimpan data Guru baru & Otomatis Membuat Akun User
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            // Identitas Pribadi (Dukcapil)
            'nik'                 => 'required|digits:16|unique:gurus,nik',
            'nama_lengkap'        => 'required|string|max:255',
            'tempat_lahir'        => 'required|string|max:100',
            'tanggal_lahir'       => 'required|date',
            'jenis_kelamin'       => 'required|in:L,P',
            'nama_ibu_kandung'    => 'required|string|max:255',

            // Status Kepegawaian (BKN)
            'nip'                 => 'nullable|digits:18|unique:gurus,nip',
            'status_kepegawaian'  => 'required|in:PNS,PPPK,GTT,GTY',
            'golongan'            => 'nullable|string|max:10',
            'jabatan'             => 'nullable|string|max:100',
            'tmt_sk'              => 'nullable|date',
            'mkg_tahun'           => 'nullable|integer|min:0',
            'mkg_bulan'           => 'nullable|integer|min:0|max:11',

            // Kualifikasi & Sertifikasi (Dapodik)
            'pendidikan_terakhir' => 'required|string|max:50',
            'nuptk'               => 'nullable|digits:16|unique:gurus,nuptk',
            'no_serdik'           => 'nullable|string|max:50',
            'nrg'                 => 'nullable|string|max:50',
        ], $this->customErrorMessages());

        // 2. Simpan Data Guru & Akun User dalam Transaction
        DB::transaction(function () use ($validatedData, $request) {
            // Gunakan NIP jika ada, jika tidak ada gunakan NIK sebagai identitas login
            $identifier = $request->filled('nip') ? $request->nip : $request->nik;
            $userEmail  = $identifier . '@sekolah.id';

            // A. Buat Akun User
            $user = User::create([
                'name'     => $request->nama_lengkap,
                'email'    => $userEmail,
                'password' => Hash::make($identifier), // Password default = NIP (atau NIK)
                'role'     => 'guru',
            ]);

            // B. Simpan Data Guru (Tambahkan user_id)
            $validatedData['user_id'] = $user->id;
            Guru::create($validatedData);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru & Akun User login berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit Guru
     */
    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Memperbarui data Guru & Nama Akun User
     */
    public function update(Request $request, Guru $guru)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            // Identitas Pribadi (Dukcapil)
            'nik'                 => 'required|digits:16|unique:gurus,nik,' . $guru->id,
            'nama_lengkap'        => 'required|string|max:255',
            'tempat_lahir'        => 'required|string|max:100',
            'tanggal_lahir'       => 'required|date',
            'jenis_kelamin'       => 'required|in:L,P',
            'nama_ibu_kandung'    => 'required|string|max:255',

            // Status Kepegawaian (BKN)
            'nip'                 => 'nullable|digits:18|unique:gurus,nip,' . $guru->id,
            'status_kepegawaian'  => 'required|in:PNS,PPPK,GTT,GTY',
            'golongan'            => 'nullable|string|max:10',
            'jabatan'             => 'nullable|string|max:100',
            'tmt_sk'              => 'nullable|date',
            'mkg_tahun'           => 'nullable|integer|min:0',
            'mkg_bulan'           => 'nullable|integer|min:0|max:11',

            // Kualifikasi & Sertifikasi (Dapodik)
            'pendidikan_terakhir' => 'required|string|max:50',
            'nuptk'               => 'nullable|digits:16|unique:gurus,nuptk,' . $guru->id,
            'no_serdik'           => 'nullable|string|max:50',
            'nrg'                 => 'nullable|string|max:50',
        ], $this->customErrorMessages());

        // 2. Update Data Guru dan User
        DB::transaction(function () use ($validatedData, $guru, $request) {
            // Update Data Guru
            $guru->update($validatedData);

            // Update nama user jika ada perubahan nama guru
            if ($guru->user) {
                $identifier = $request->filled('nip') ? $request->nip : $request->nik;
                
                $guru->user->update([
                    'name'  => $request->nama_lengkap,
                    'email' => $identifier . '@sekolah.id',
                ]);
            }
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diperbarui.');
    }

    /**
     * Menghapus data Guru beserta Akun User-nya
     */
    public function destroy(Guru $guru)
    {
        DB::transaction(function () use ($guru) {
            // Hapus Akun User terlebih dahulu jika terhubung
            if ($guru->user) {
                $guru->user->delete();
            }

            // Hapus Data Guru
            $guru->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru dan Akun User berhasil dihapus.');
    }

    /**
     * Fitur Reset Password Akun Guru ke NIP / NIK Default
     */
    public function resetPassword(Guru $guru)
    {
        if (!$guru->user) {
            return back()->with('error', 'Akun user untuk guru ini tidak ditemukan.');
        }

        $identifier = $guru->nip ?? $guru->nik;

        $guru->user->update([
            'password' => Hash::make($identifier),
        ]);

        return back()->with('success', "Password akun {$guru->nama_lengkap} berhasil di-reset ke default ({$identifier}).");
    }

    /**
     * Custom Error Messages
     */
    private function customErrorMessages()
    {
        return [
            'nik.required'          => 'NIK wajib diisi.',
            'nik.digits'            => 'NIK harus berjumlah 16 digit angka.',
            'nik.unique'            => 'NIK sudah terdaftar.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi (sesuai Akta).',
            'nip.digits'            => 'NIP harus berjumlah 18 digit angka.',
            'nip.unique'            => 'NIP sudah terdaftar.',
            'nuptk.digits'          => 'NUPTK harus berjumlah 16 digit.',
            'nuptk.unique'           => 'NUPTK sudah terdaftar.',
        ];
    }
}