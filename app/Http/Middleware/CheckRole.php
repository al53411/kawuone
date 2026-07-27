<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Superadmin bypass: Selalu izinkan superadmin tanpa mengecek kriteria lain
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // 3. Cek apakah role user terdaftar pada kriteria route yang diakses
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Jika role tidak cocok, batalkan akses (Forbidden)
        abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
    }
}