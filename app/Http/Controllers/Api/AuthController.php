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
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role, // Penting agar Android tahu role user (superadmin/guru/siswa)
                ],
                'token'   => null // Tambahkan ini agar tidak crash di Android jika memanggil data token
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }
}