<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan semua daftar pengguna (Kepala Sekolah / User lain)
     */
    public function index(Request $request)
    {
        $query = User::with('sekolah');

        // Filter berdasarkan pencarian nama atau email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Ambil data user dengan pagination
        $users = $query->latest()->paginate(10);

        return view('superadmin.kepsek.index', compact('users'));
    }

    /**
     * Tampilkan form pembuatan akun Kepala Sekolah baru
     */
    public function create()
    {
        // Ambil data sekolah untuk di-looping pada dropdown <select> di Blade
        $sekolahs = Sekolah::select('id', 'nama_sekolah', 'npsn', 'nama_kepsek', 'nip_kepsek', 'email')
                           ->orderBy('nama_sekolah', 'asc')
                           ->get();

        return view('superadmin.kepsek.create', compact('sekolahs'));
    }

    /**
     * Simpan akun Kepala Sekolah baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'sekolah_id' => 'required|exists:sekolahs,id',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email',
            'nip'        => 'nullable|string|max:50',
            'password'   => 'required|string|min:8|confirmed',
        ], [
            'sekolah_id.required' => 'Pilih unit sekolah terlebih dahulu.',
            'sekolah_id.exists'   => 'Sekolah yang dipilih tidak valid.',
            'name.required'       => 'Nama lengkap wajib diisi.',
            'email.required'      => 'Alamat email wajib diisi.',
            'email.unique'        => 'Email ini sudah terdaftar di sistem.',
            'password.required'   => 'Password wajib diisi.',
            'password.min'        => 'Password minimal berisi 8 karakter.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
        ]);

        // 2. Buat User Baru
        User::create([
            'sekolah_id' => $validatedData['sekolah_id'],
            'name'       => $validatedData['name'],
            'email'      => $validatedData['email'],
            'nip'        => $validatedData['nip'] ?? null,
            'password'   => Hash::make($validatedData['password']),
            'role'       => 'kepsek', // Set role otomatis menjadi kepsek
        ]);

        // 3. Redirect Kembali dengan Pesan Sukses
        return redirect()->route('superadmin.sekolah.index')
                         ->with('success', 'Akun Kepala Sekolah berhasil dibuat!');
    }

    /**
     * Tampilkan detail pengguna
     */
    public function show(string $id)
    {
        $user = User::with('sekolah')->findOrFail($id);
        return view('superadmin.kepsek.show', compact('user'));
    }

    /**
     * Tampilkan form edit pengguna
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $sekolahs = Sekolah::orderBy('nama_sekolah', 'asc')->get();

        return view('superadmin.kepsek.edit', compact('user', 'sekolahs'));
    }

    /**
     * Update data pengguna di database
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'sekolah_id' => 'required|exists:sekolahs,id',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $id,
            'nip'        => 'nullable|string|max:50',
            'password'   => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique'       => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Update data dasar
        $user->sekolah_id = $validatedData['sekolah_id'];
        $user->name       = $validatedData['name'];
        $user->email      = $validatedData['email'];
        $user->nip        = $validatedData['nip'] ?? null;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('superadmin.sekolah.index')
                         ->with('success', 'Data akun Kepala Sekolah berhasil diperbarui!');
    }

    /**
     * Hapus pengguna dari database
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('superadmin.sekolah.index')
                         ->with('success', 'Akun Kepala Sekolah berhasil dihapus!');
    }
}