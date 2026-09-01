<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .glass-card {
        background: rgba(255, 255, 255, 0.35);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans bg-contain bg-center bg-no-repeat"
      style="background-image: url('{{ asset('bg-img.png') }}'); background-size: 85% auto;">

    <!-- Overlay Gelap Transparan (Membuat Kartu Login Lebih Kontras) -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] pointer-events-none"></div>

    <!-- ================= SPLASH SCREEN START ================= -->
    <div id="splash-screen"
        class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900/90 backdrop-blur-md transition-opacity duration-300 text-white opacity-100">
        <div class="flex flex-col items-center animate-pulse">
            <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-20 h-20 mb-4 object-contain">
            <h2 class="text-2xl font-bold tracking-wider mb-3">ADkwOne</h2>
            <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
    <!-- ================= SPLASH SCREEN END ================= -->

    <!-- Efek Background Dekoratif Cahaya -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Kotak Login Glassmorphism -->
    <div class="w-full max-w-md glass-card rounded-3xl shadow-2xl p-8 z-10">

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl mx-auto flex items-center justify-center mb-3">
                <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-full h-full object-contain drop-shadow">
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">E-ADM</h2>
        </div>

        <!-- Session Status (Error Alert) -->
        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50/70 backdrop-blur-sm border border-red-200/50 text-red-600 rounded-xl text-sm">
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
                <label for="login" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Email / NIP</label>
                <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                    autocomplete="username"
                    class="w-full px-4 py-3 bg-white/60 border border-white/40 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white/90 transition-all text-sm text-slate-800 placeholder-slate-400"
                    placeholder="Masukkan NIP atau Email">
            </div>

            <!-- Input Password -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Password</label>
                </div>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-3 bg-white/60 border border-white/40 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white/90 transition-all text-sm text-slate-800 placeholder-slate-400"
                    placeholder="••••••••">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-sm text-slate-700 cursor-pointer font-medium">
                    <input type="checkbox" name="remember"
                        class="rounded border-white/40 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white/50 mr-2">
                    Ingat Saya
                </label>
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all duration-200 text-sm tracking-wide mt-2">
                Masuk Sistem
            </button>
        </form>

    </div>

    <!-- Script Kontrol Splash Screen -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const splash = document.getElementById('splash-screen');
        if (splash) {
            setTimeout(() => {
                splash.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                    splash.style.display = 'none';
                }, 300);
            }, 400);
        }
    });
    </script>
</body>

</html>