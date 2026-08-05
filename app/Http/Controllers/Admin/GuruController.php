<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema; // <-- DITAMBAHKAN AGAR TIDAK ERROR SCHEMA

class GuruController extends Controller
{
    /**
     * Menampilkan daftar data Guru (Difilter per Sekolah)
        */
    public function index(Request $request)
    {
        $sekolahId = Auth::user()->sekolah_id;

        $query = Guru::where('sekolah_id', $sekolahId);

        // Fitur Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Fitur Filter Status
        if ($request->filled('status')) {
            $query->where('status_kepegawaian', $request->status);
        }

        $gurus = $query->latest()->paginate(10)->withQueryString();

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

            // Ambil sekolah_id milik Admin yang sedang menambahkan
            $sekolahId = Auth::user()->sekolah_id;

            // A. Buat Akun User (Diikat dengan sekolah_id & NIP)
            $user = User::create([
                'name'       => $request->nama_lengkap,
                'nip'        => $request->nip,
                'email'      => $userEmail,
                'password'   => Hash::make($identifier), // Password default = NIP (atau NIK)
                'role'       => 'guru',
                'sekolah_id' => $sekolahId,
            ]);

            // B. Simpan Data Guru
            $validatedData['user_id'] = $user->id;
            if (Schema::hasColumn('gurus', 'sekolah_id')) {
                $validatedData['sekolah_id'] = $sekolahId;
            }
            Guru::create($validatedData);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru & Akun User login berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit Guru
     */
    public function edit(Guru $guru)
    {
        // Proteksi: Mencegah admin mengakses/mengedit guru milik sekolah lain via URL
        $this->authorizeSekolah($guru);

        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Memperbarui data Guru & Nama Akun User
     */
    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nik'                 => 'required|digits:16|unique:gurus,nik,' . $guru->id,
            'nama_lengkap'        => 'required|string|max:255',
            'tempat_lahir'        => 'required|string',
            'tanggal_lahir'       => 'required|date',
            'jenis_kelamin'       => 'required|in:L,P',
            'nama_ibu_kandung'    => 'required|string',
            'mata_pelajaran'      => 'required|string',
            'status_kepegawaian'  => 'required|string',
            'pendidikan_terakhir' => 'required|string',
        ]);

        $guru->update($request->all());

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui!');
    }

    /**
     * Menghapus data Guru beserta Akun User-nya
     */
    public function destroy(Guru $guru)
    {
        // Proteksi Sekolah
        $this->authorizeSekolah($guru);

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
        // Proteksi Sekolah
        $this->authorizeSekolah($guru);

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
     * Helper Function: Memastikan Guru berasal dari sekolah yang sama dengan Admin yang sedang Login
     */
    private function authorizeSekolah(Guru $guru)
    {
        $adminSekolahId = Auth::user()->sekolah_id;
        $guruSekolahId  = $guru->user->sekolah_id ?? $guru->sekolah_id ?? null;

        abort_if($guruSekolahId !== $adminSekolahId, 403, 'Anda tidak memiliki akses untuk mengelola data guru dari sekolah lain.');
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