<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // Fitur Filter Status Kepegawaian
        if ($request->filled('status')) {
            $query->where('status_kepegawaian', $request->status);
        }

        $gurus = $query->latest()->paginate(10)->withQueryString();

        // --- LOGIKA CEK TUGAS / WALI KELAS / GURU MAPEL ---
        $sekolahKelas = Kelas::where('sekolah_id', $sekolahId)->get();

        $gurus->getCollection()->transform(function ($guru) use ($sekolahKelas) {
            // 1. Cek apakah guru bertindak sebagai Guru Mata Pelajaran
            $jabatanLower = strtolower($guru->jabatan ?? '');
            $jenisGuruLower = strtolower($guru->jenis_guru ?? '');
            
            $isGuruMapel = str_contains($jabatanLower, 'mapel') || 
                           str_contains($jabatanLower, 'mata pelajaran') || 
                           str_contains($jenisGuruLower, 'mapel') || 
                           !empty($guru->mata_pelajaran);

            // 2. Cari kelas mana saja yang menugaskan guru ini sebagai Wali Kelas
            $assignedClasses = $sekolahKelas->filter(function ($kelas) use ($guru) {
                $waliVal = $kelas->wali_kelas ?? null;
                $guruIdVal = $kelas->guru_id ?? null;
                $waliIdVal = $kelas->wali_kelas_id ?? null;

                return in_array($guru->id, [$waliVal, $guruIdVal, $waliIdVal]) ||
                       in_array($guru->user_id, [$waliVal, $guruIdVal, $waliIdVal]) ||
                       ($guru->nama_lengkap && $waliVal == $guru->nama_lengkap);
            });

            // 3. Properti Tambahan untuk dikirim ke Blade View
            $guru->assigned_kelas = $assignedClasses;
            $guru->is_guru_mapel = $isGuruMapel;

            if ($isGuruMapel) {
                $guru->tipe_penugasan = 'guru_mapel';
                $guru->has_kelas = true; 
            } elseif ($assignedClasses->isNotEmpty()) {
                $guru->tipe_penugasan = 'wali_kelas';
                $guru->has_kelas = true;
            } else {
                $guru->tipe_penugasan = 'none';
                $guru->has_kelas = false; 
            }

            return $guru;
        });

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
            'jenis_guru'          => 'nullable|string|max:50',
            'mata_pelajaran'      => 'nullable|string|max:100',
            'tmt_sk'              => 'nullable|date', // ✅ Validasi tipe tanggal
            'mkg_tahun'           => 'nullable|integer|min:0',
            'mkg_bulan'           => 'nullable|integer|min:0|max:11',

            // Kualifikasi & Sertifikasi (Dapodik)
            'pendidikan_terakhir' => 'required|string|max:50',
            'nuptk'               => 'nullable|digits:16|unique:gurus,nuptk',
            'no_serdik'           => 'nullable|string|max:50',
            'nrg'                 => 'nullable|string|max:50',
        ], $this->customErrorMessages());

        // Ubah string kosong "" pada field date menjadi NULL agar tidak terkirim sebagai invalid date di MySQL
        if (empty($validatedData['tmt_sk'])) {
            $validatedData['tmt_sk'] = null;
        }

        // 2. Simpan Data Guru & Akun User dalam Transaction
        DB::transaction(function () use ($validatedData, $request) {
            $identifier = $request->filled('nip') ? $request->nip : $request->nik;
            $userEmail  = $identifier . '@sekolah.id';

            $sekolahId = Auth::user()->sekolah_id;

            // A. Buat Akun User
            $user = User::create([
                'name'       => $request->nama_lengkap,
                'nip'        => $request->nip,
                'email'      => $userEmail,
                'password'   => Hash::make($identifier),
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

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru & Akun User login berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit Guru
     */
    public function edit(Guru $guru)
    {
        $this->authorizeSekolah($guru);

        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Memperbarui data Guru & Nama Akun User
     */
    public function update(Request $request, Guru $guru)
    {
        $validatedData = $request->validate([
            'nik'                 => 'required|digits:16|unique:gurus,nik,' . $guru->id,
            'nip'                 => 'nullable|digits:18|unique:gurus,nip,' . $guru->id,
            'nuptk'               => 'nullable|digits:16|unique:gurus,nuptk,' . $guru->id,
            'nama_lengkap'        => 'required|string|max:255',
            'tempat_lahir'        => 'required|string',
            'tanggal_lahir'       => 'required|date',
            'jenis_kelamin'       => 'required|in:L,P',
            'nama_ibu_kandung'    => 'required|string',
            'status_kepegawaian'  => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'jabatan'             => 'nullable|string',
            'jenis_guru'          => 'nullable|string',
            'mata_pelajaran'      => 'nullable|string',
            'tmt_sk'              => 'nullable|date', // ✅ Mencegah error format tahun invalid seperti 20019
            'mkg_tahun'           => 'nullable|integer|min:0',
            'mkg_bulan'           => 'nullable|integer|min:0|max:11',
            'no_serdik'           => 'nullable|string|max:50',
            'nrg'                 => 'nullable|string|max:50',
        ], $this->customErrorMessages());

        // Ubah string kosong "" pada field date menjadi NULL
        if (empty($validatedData['tmt_sk'])) {
            $validatedData['tmt_sk'] = null;
        }

        $guru->update($validatedData);

        // Update nama user jika terikat
        if ($guru->user) {
            $guru->user->update(['name' => $request->nama_lengkap]);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui!');
    }

    /**
     * Menghapus data Guru beserta Akun User-nya
     */
    public function destroy(Guru $guru)
    {
        $this->authorizeSekolah($guru);

        DB::transaction(function () use ($guru) {
            if ($guru->user) {
                $guru->user->delete();
            }
            $guru->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru dan Akun User berhasil dihapus!');
    }

    /**
     * Fitur Reset Password Akun Guru
     */
    public function resetPassword(Guru $guru)
    {
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

    private function authorizeSekolah(Guru $guru)
    {
        $adminSekolahId = Auth::user()->sekolah_id;
        $guruSekolahId  = $guru->user->sekolah_id ?? $guru->sekolah_id ?? null;

        abort_if($guruSekolahId !== $adminSekolahId, 403, 'Anda tidak memiliki akses untuk mengelola data guru dari sekolah lain.');
    }

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
            'nuptk.unique'          => 'NUPTK sudah terdaftar.',
            'tmt_sk.date'           => 'Format TMT SK harus berupa tanggal yang valid (YYYY-MM-DD).',
            'tanggal_lahir.date'    => 'Format Tanggal Lahir harus berupa tanggal yang valid (YYYY-MM-DD).',
        ];
    }
}