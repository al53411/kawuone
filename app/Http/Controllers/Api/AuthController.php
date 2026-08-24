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
        $request->validate([
            'email'    => 'required', // Bisa berisi Email atau NIP
            'password' => 'required',
        ]);

        $loginInput = $request->email;
        
        // Cek apakah input berupa NIP (angka) atau Email
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'email';
        
        // Jika inputan angka NIP saja (misal: 198501012010011001), otomatis tambahkan domain @sekolah.id
        if (!filter_var($loginInput, FILTER_VALIDATE_EMAIL) && is_numeric($loginInput)) {
            $loginInput = $loginInput . '@sekolah.id';
        }

        if (Auth::attempt(['email' => $loginInput, 'password' => $request->password])) {
            /** @var User $user */
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => strtolower($user->role),
                ],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'NIP/Email atau Password salah.',
        ], 401);
    }
}