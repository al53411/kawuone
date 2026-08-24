<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role, // Mengirimkan role dari DB ('guru', 'kepsek', 'admin', dll)
                ],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau Password salah.',
        ], 401);
    }
}