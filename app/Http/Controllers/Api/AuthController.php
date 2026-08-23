<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user'    => [
                    'id'    => (int) $user->id,
                    'name'  => (string) $user->name,
                    'email' => (string) $user->email,
                    'role'  => (string) ($user->role ?? 'user'),
                ],
                'token'   => '' // Ubah null menjadi string kosong '' agar Retrofit tidak menganggapnya crash
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah',
            'user'    => null, // Tambahkan ini agar struktur JSON respons konsisten
            'token'   => null
        ], 401);
    }
}