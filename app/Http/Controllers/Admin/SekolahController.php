<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    /**
     * Tampilkan Profil Sekolah milik user yang sedang login
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Jika Superadmin, ambil data sekolah pertama
        if ($user->role === 'superadmin') {
            $sekolah = Sekolah::first();
        } else {
            // 2. Jika Admin Sekolah / Kepsek, ambil data berdasarkan sekolah_id user
            $sekolah = $user->sekolah_id ? Sekolah::find($user->sekolah_id) : null;
        }

        // Jika data sekolah belum terikat, ambil sekolah pertama sebagai fallback (ATAU buat dummy/kosong)
        if (!$sekolah) {
            // Option A: Ambil sekolah pertama secara otomatis jika sekolah_id user masih null
            $sekolah = Sekolah::first();

            // Option B: Jika di database memang belum ada 1 pun data sekolah
            if (!$sekolah) {
                return view('admin.sekolah.index', [
                    'sekolah' => new Sekolah(), // Kirim objek kosong agar view tidak crash
                    'warning' => 'Akun Anda belum terhubung dengan unit sekolah. Silakan isi profil sekolah pertama Anda.'
                ]);
            }
        }

        return view('admin.sekolah.index', compact('sekolah'));
    }

    /**
     * Update Data Sekolah
     */
    public function update(Request $request, $id)
    {
        // Jika ID bernilai 0 atau tidak ada (buat baru / update)
        $sekolah = Sekolah::find($id) ?? new Sekolah();
        $user = auth()->user();

        // A. JIKA USER BUKAN SUPERADMIN (ADMIN SEKOLAH / KEPSEK)
        if ($user->role !== 'superadmin') {
            $validated = $request->validate([
                'telepon' => 'nullable|string|max:50',
                'email'   => 'nullable|email|max:255',
            ]);

            $sekolah->update($validated);

            return redirect()->back()->with('success', 'Kontak sekolah berhasil diperbarui!');
        }

        // B. JIKA USER ADALAH SUPERADMIN
        $validated = $request->validate([
            'npsn'           => 'required|numeric|unique:sekolahs,npsn,' . ($sekolah->id ?? 'NULL'),
            'nama_sekolah'   => 'required|string|max:255',
            'jenjang'        => 'required|string',
            'status'         => 'required|string',
            'alamat'         => 'required|string',
            'desa_kelurahan' => 'nullable|string|max:255',
            'kecamatan'      => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi'       => 'nullable|string|max:255',
            'nama_kepsek'    => 'nullable|string|max:255',
            'nip_kepsek'     => 'nullable|string|max:255',
            'telepon'        => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
        ]);

        $sekolah->fill($validated)->save();

        // Hubungkan akun user jika sebelumnya null
        if (!$user->sekolah_id) {
            $user->update(['sekolah_id' => $sekolah->id]);
        }

        return redirect()->back()->with('success', 'Seluruh data sekolah berhasil diperbarui!');
    }
}