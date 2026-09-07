<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SekolahController extends Controller
{
    /**
     * Tampilkan Profil Sekolah (Read-Only) milik guru yang sedang login
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil data sekolah berdasarkan sekolah_id milik guru yang login
        $sekolah = ($user && $user->sekolah_id) 
            ? Sekolah::find($user->sekolah_id) 
            : Sekolah::first(); // Fallback jika guru belum terikat ke ID sekolah tertentu

        // Jika data sekolah tidak ditemukan di database
        if (!$sekolah) {
            $sekolah = new Sekolah(); // Objek kosong agar Blade view tidak error
            $warningMsg = 'Akun Anda belum terhubung dengan unit sekolah manapun.';

            return view('guru.sekolah.index', [
                'sekolah' => $sekolah,
                'warning' => $warningMsg
            ]);
        }

        return view('guru.sekolah.index', compact('sekolah'));
    }
}