<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .glass-card {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    </style>
</head>

<body
    class="bg-gradient-to-br from-slate-100 via-indigo-100/40 to-slate-200 min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Efek Background Dekoratif -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-400/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-400/30 rounded-full blur-3xl pointer-events-none">
    </div>

    <!-- Kotak Login Glassmorphism -->
    <div class="w-full max-w-md glass-card rounded-3xl shadow-2xl p-8 z-10 animate-fade-in">

        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 bg-gradient-to-tr from-blue-500 to-indigo-600 rounded-2xl mx-auto flex items-center justify-center shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">ADkwOne</h2>
        </div>

        <!-- Session Status (Error Alert) -->
        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50/50 backdrop-blur-sm border border-red-200/50 text-red-600 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Input Email / NIP -->
            <div>
                <label for="login"
                    class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Email / NIP</label>
                <!-- name dan old disesuaikan ke "login" agar cocok dengan Controller -->
                <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                    autocomplete="username"
                    class="w-full px-4 py-3 bg-white/50 border border-white/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white/80 transition-all text-sm text-slate-800"
                    placeholder="Masukkan NIP atau Email">
            </div>

            <!-- Input Password -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password"
                        class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">Password</label>
                </div>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-3 bg-white/50 border border-white/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white/80 transition-all text-sm text-slate-800"
                    placeholder="••••••••">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-sm text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember"
                        class="rounded border-white/40 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white/50 mr-2">
                    Ingat Saya
                </label>
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all duration-200 text-sm tracking-wide mt-2">
                Masuk Sistem
            </button>
        </form>

    </div>
</body>

</html>