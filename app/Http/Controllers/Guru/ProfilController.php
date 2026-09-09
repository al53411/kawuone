<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru; // Sesuaikan dengan nama Model Guru Anda
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Mengambil data guru yang terhubung dengan user yang sedang login
        // Opsi A: Jika relasi user -> guru menggunakan 'user_id'
        $guru = Guru::where('user_id', $user->id)->first();

        // Opsi B: Jika relasi dicocokkan berdasarkan email (opsional alternatif)
        // $guru = Guru::where('email', $user->email)->first();

        return view('guru.profil.index', compact('user', 'guru'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'nama' => 'required|string|max:255',
            'nip'  => 'nullable|string|max:30',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        // Update data guru
        $guru->update([
            'nama'   => $request->nama,
            'nip'    => $request->nip,
            'no_hp'  => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}