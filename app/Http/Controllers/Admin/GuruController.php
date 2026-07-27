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
    public function index()
    {
        $gurus = Guru::latest()->get();
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'          => 'required|unique:gurus,nip|unique:users,nip|max:30',
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:100',
            'golongan'     => 'required|string|max:50',
        ]);

        // Gunakan DB Transaction agar pembuatan Guru & User berjalan beriringan
        DB::transaction(function () use ($validated) {
            // 1. Simpan ke tabel 'gurus'
            Guru::create([
                'nip'          => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'], // Menggunakan 'nama_lengkap' sesuai migration
                'jabatan'      => $validated['jabatan'],
                'golongan'     => $validated['golongan'],
            ]);

            // 2. Buat Akun User untuk Login
            User::create([
                'name'     => $validated['nama_lengkap'],
                'nip'      => $validated['nip'],
                'role'     => 'guru', // Role diset otomatis jadi 'guru'
                'password' => Hash::make($validated['nip']), // Password awal = NIP
            ]);
        });

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data Guru dan Akun Login berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, string $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nip'          => 'required|max:30|unique:gurus,nip,' . $guru->id,
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:100',
            'golongan'     => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($guru, $validated) {
            $oldNip = $guru->nip;

            // Update tabel gurus
            $guru->update([
                'nip'          => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jabatan'      => $validated['jabatan'],
                'golongan'     => $validated['golongan'],
            ]);

            // Sync update ke akun User-nya juga
            $user = User::where('nip', $oldNip)->first();
            if ($user) {
                $user->update([
                    'name' => $validated['nama_lengkap'],
                    'nip'  => $validated['nip'],
                ]);
            }
        });

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data Guru berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $guru = Guru::findOrFail($id);

        DB::transaction(function () use ($guru) {
            // Hapus akun user-nya juga saat data guru dihapus
            User::where('nip', $guru->nip)->delete();
            $guru->delete();
        });

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data Guru dan Akun Login berhasil dihapus.');
    }
}