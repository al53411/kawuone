<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input dari form
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'Email atau NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password   = $request->input('password');

        // 2. Tentukan Email / User yang dicoba untuk Login
        $user = null;

        if (str_contains($loginInput, '@')) {
            // Jika input berupa Email
            $user = User::where('email', $loginInput)->first();
        } else {
            // Jika input berupa NIP/NIK
            $user = User::whereHas('guru', function ($query) use ($loginInput) {
                $query->where('nip', $loginInput)->orWhere('nik', $loginInput);
            })->first();

            if (!$user) {
                $user = User::where('email', 'LIKE', $loginInput . '@%')->first();
            }
        }

        // 3. Verifikasi User & Password
        if ($user && Hash::check($password, $user->password)) {
            // Login user secara manual
            Auth::login($user, $request->boolean('remember'));
            
            // Hapus intended URL lama & regenerasi session ID
            $request->session()->forget('url.intended');
            $request->session()->regenerate();

            // 🟢 SET FLASH SESSION DI SINI (Setelah session()->regenerate())
            session()->flash('login_success', true);
            session()->flash('user_name', $user->name ?? $user->nama ?? 'Pengguna');

            // Redirection berdasarkan role
            if ($user->role === 'superadmin') {
                return redirect('/superadmin/dashboard');
            }

            if (in_array($user->role, ['admin', 'admin_sekolah', 'kepsek'])) {
                return redirect('/admin/dashboard');
            }

            if ($user->role === 'guru') {
                return redirect('/guru/dashboard');
            }

            return redirect('/dashboard');
        }

        // 4. Jika login gagal
        return back()->withErrors([
            'login' => 'NIP/Email atau password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}