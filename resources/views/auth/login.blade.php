<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Font Awesome untuk Icon Password -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* --- ANIMASI GELOMBANG START --- */
        .waves-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 15vh;
            min-height: 100px;
            max-height: 150px;
            z-index: 1;
            pointer-events: none;
        }

        .parallax > use {
            animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
        }
        .parallax > use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 7s;
        }
        .parallax > use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 10s;
        }
        .parallax > use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 13s;
        }
        .parallax > use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 20s;
        }

        @keyframes move-forever {
            0% {
                transform: translate3d(-90px, 0, 0);
            }
            100% {
                transform: translate3d(85px, 0, 0);
            }
        }

        /* Penyesuaian tinggi gelombang di layar HP */
        @media (max-width: 768px) {
            .waves-container {
                height: 40px;
                min-height: 40px;
            }
        }
        /* --- ANIMASI GELOMBANG END --- */
    </style>
</head>

<body class="min-h-screen min-h-[100dvh] w-full flex items-center justify-center p-4 sm:p-6 relative overflow-hidden font-sans bg-slate-950 text-slate-800">

    <!-- Background Image -->
    <div class="fixed inset-0 w-full h-full bg-contain bg-center bg-no-repeat opacity-50 md:opacity-70 pointer-events-none"
         style="background-image: url('{{ asset('bg-img.png') }}');"></div>

    <!-- Overlay Gelap Uniform -->
    <div class="fixed inset-0 w-full h-full bg-slate-950/60 backdrop-blur-[2px] pointer-events-none"></div>

    <!-- ================= SPLASH SCREEN START ================= -->
    <div id="splash-screen"
        class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900/90 backdrop-blur-md transition-opacity duration-300 text-white opacity-100">
        <div class="flex flex-col items-center animate-pulse">
            <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-16 h-16 sm:w-20 sm:h-20 mb-4 object-contain">
            <h2 class="text-xl sm:text-2xl font-bold tracking-wider mb-3">ADkwOne</h2>
            <div class="w-7 h-7 sm:w-8 sm:h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
    <!-- ================= SPLASH SCREEN END ================= -->

    <!-- Efek Background Dekoratif Cahaya -->
    <div class="fixed -top-24 -left-24 sm:-top-40 sm:-left-40 w-64 h-64 sm:w-96 sm:h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-24 -right-24 sm:-bottom-40 sm:-right-40 w-64 h-64 sm:w-96 sm:h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Kotak Login Glassmorphism -->
    <div class="w-full max-w-sm sm:max-w-md glass-card rounded-2xl sm:rounded-3xl shadow-2xl p-6 sm:p-8 z-10 my-auto relative">

        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl mx-auto flex items-center justify-center mb-2 sm:mb-3 p-1">
                <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-full h-full object-contain drop-shadow-md">
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">E-ADM</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">Silakan masuk ke akun Anda</p>
        </div>

        <!-- Session Status (Error Alert) -->
        @if ($errors->any())
        <div class="mb-5 p-3.5 sm:p-4 bg-red-50/80 backdrop-blur-sm border border-red-200 text-red-700 rounded-xl text-xs sm:text-sm shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
            @csrf

            <!-- Input Email / NIP -->
            <div>
                <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email / NIP</label>
                <div class="relative">
                    <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                        autocomplete="username"
                        class="w-full px-4 py-2.5 sm:py-3 bg-white/70 border border-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-sm text-slate-800 placeholder-slate-400 shadow-sm"
                        placeholder="Masukkan NIP atau Email">
                </div>
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full pl-4 pr-10 py-2.5 sm:py-3 bg-white/70 border border-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-sm text-slate-800 placeholder-slate-400 shadow-sm"
                        placeholder="••••••••">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-700 focus:outline-none">
                        <i class="fa-solid fa-eye text-sm" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-xs sm:text-sm text-slate-700 cursor-pointer font-medium select-none">
                    <input type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white/70 mr-2">
                    Ingat Saya
                </label>
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-[0.99] text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all duration-200 text-sm tracking-wide mt-2">
                Masuk Sistem
            </button>
        </form>

    </div>

    <!-- ================= ANIMASI GELOMBANG SVG START ================= -->
    <div class="waves-container">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(99, 102, 241, 0.15)" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(59, 130, 246, 0.25)" />
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(99, 102, 241, 0.1)" />
                <use xlink:href="#gentle-wave" x="48" y="7" fill="rgba(30, 41, 59, 0.4)" />
            </g>
        </svg>
    </div>
    <!-- ================= ANIMASI GELOMBANG SVG END ================= -->

    <!-- Script Kontrol Splash Screen & Password Toggle -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Splash Screen
        const splash = document.getElementById('splash-screen');
        if (splash) {
            setTimeout(() => {
                splash.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                    splash.style.display = 'none';
                }, 300);
            }, 400);
        }

        // Toggle Visibility Password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', function() {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.classList.toggle('fa-eye', !isPassword);
                eyeIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }
    });
    </script>
</body>

</html>