<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\JurnalGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class KepalaSekolahController extends Controller
{
    /**
     * Tampilkan daftar Kepala Sekolah.
     */
    public function index()
    {
        $kepalaSekolahs = User::where('role', 'kepsek')
            ->with('sekolah') // Mengambil relasi data sekolah
            ->latest()
            ->paginate(10);

        return view('superadmin.kepsek.index', compact('kepalaSekolahs'));
    }

    /**
     * Menampilkan form tambah akun Kepala Sekolah.
     */
    public function create()
    {
        // Ambil semua daftar sekolah untuk opsi pada dropdown
        $sekolahs = Sekolah::orderBy('nama_sekolah', 'asc')->get();

        // Mengembalikan view superadmin.kepsek.create
        return view('superadmin.kepsek.create', compact('sekolahs'));
    }

    /**
     * Menyimpan data akun Kepala Sekolah baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Form
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'nip'        => ['nullable', 'string', 'max:30', 'unique:users,nip'],
        ], [
            // Custom Message Bahasa Indonesia
            'name.required'       => 'Nama lengkap wajib diisi.',
            'email.required'      => 'Email wajib diisi.',
            'email.unique'        => 'Email tersebut sudah terdaftar di sistem.',
            'password.required'   => 'Password wajib diisi.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'sekolah_id.required' => 'Silakan pilih unit sekolah terlebih dahulu.',
            'sekolah_id.exists'   => 'Unit sekolah yang dipilih tidak valid.',
            'nip.unique'          => 'NIP/NIK tersebut sudah terdaftar.',
        ]);

        // 2. Simpan Data User Baru dengan Role Kepsek
        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'kepsek',
            'sekolah_id' => $request->sekolah_id,
            'nip'        => $request->nip,
        ]);

        // 3. Redirect Kembali dengan Notifikasi Sukses
        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', 'Akun Kepala Sekolah "' . $request->name . '" berhasil dibuat!');
    }

    /**
     * Menampilkan daftar validasi jurnal guru untuk Kepala Sekolah.
     */
    public function indexValidasiJurnal(Request $request)
    {
        $status = $request->input('status', 'Pending'); 

        $jurnals = JurnalGuru::with(['guru', 'kelas'])
            ->when($status, function ($query, $status) {
                return $query->where('status_validasi', $status);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('admin.kepala-sekolah.validasi_jurnal', compact('jurnals', 'status'));
    }

    /**
     * Mengupdate status validasi jurnal.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status_validasi' => 'required|in:Disetujui,Ditolak',
            'catatan_kepsek'  => $request->status_validasi == 'Ditolak' ? 'required|string|min:5' : 'nullable|string',
        ]);

        $jurnal = JurnalGuru::findOrFail($id);
        
        $jurnal->update([
            'status_validasi' => $request->status_validasi,
            'catatan_kepsek'  => $request->status_validasi == 'Ditolak' ? $request->catatan_kepsek : null,
            'tanggal_validasi'=> now(),
        ]);

        return redirect()->back()->with('success', 'Status validasi jurnal berhasil diperbarui.');
    }
}