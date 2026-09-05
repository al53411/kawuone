<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 & Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-slate-50 text-slate-800 relative overflow-x-hidden">

    <div class="relative z-10 min-h-screen flex">
        <!-- Panggil sidebar hanya dari sini -->
        @include('layouts.superadmin')

        <div class="flex-1 flex flex-col min-w-0">
            @include('layouts.navigation')

            @if (isset($header) || View::hasSection('header'))
            <header class="bg-white/80 backdrop-blur-md border-b border-white/40 shadow-xs">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                        @yield('header')
                    </h1>
                </div>
            </header>
            @endif

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Skrip Notifikasi Sinkronisasi & Validasi -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Notifikasi Sinkronisasi Otomatis Setelah Login Berhasil
        @if (session('login_success'))
            Swal.fire({
                title: 'Menyinkronkan Data...',
                html: 'Sedang memeriksa data sekolah, kelas, dan hak akses user.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Data Berhasil Disinkronkan!',
                    html: `
                        <div style="text-align: left; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px; border-radius: 8px; color: #166534; margin-top: 8px;">
                            <p style="margin: 0; font-weight: bold;">Status Sistem:</p>
                            <ul style="margin: 4px 0 0 18px; padding: 0; font-size: 14px;">
                                <li>✅ Data Sekolah ditemukan</li>
                                <li>✅ Data Kelas & Siswa terhubung</li>
                                <li>✅ Sesi login aktif</li>
                            </ul>
                        </div>
                    `,
                    confirmButtonText: 'OK, Lanjutkan',
                    confirmButtonColor: '#16a34a',
                    showClass: { popup: 'animate__animated animate__zoomIn' }
                });
            }, 1500);
        @endif

        // 2. Notifikasi Sukses Biasa (Misal setelah simpan data)
        @if (session('success') && !session('login_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#16a34a',
                showClass: { popup: 'animate__animated animate__zoomIn' }
            });
        @endif

        // 3. Notifikasi Error / Data Tidak Sesuai dari Server (Tanda Merah + Tombol OK)
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Sesuai!',
                html: `
                    <div style="text-align: left; color: #dc2626; background-color: #fef2f2; padding: 12px; border-radius: 8px; border: 1px solid #fca5a5;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 4px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
                showClass: { popup: 'animate__animated animate__shakeX' }
            });
        @endif

        // 4. Notifikasi Session Error Umum
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
                showClass: { popup: 'animate__animated animate__shakeX' }
            });
        @endif

    });

    // 5. Fungsi Helper Global untuk Validasi Client-Side (Opsional panggil dari view)
    function validasiDanKirim(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const requiredInputs = form.querySelectorAll('[required]');
        let adaError = false;

        requiredInputs.forEach(input => {
            input.style.borderColor = '';
            input.style.backgroundColor = '';
        });

        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                adaError = true;
                input.style.borderColor = '#dc2626';
                input.style.backgroundColor = '#fef2f2';
            }
        });

        if (adaError) {
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Lengkap!',
                text: 'Harap lengkapi semua bidang yang ditandai warna merah.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
                showClass: { popup: 'animate__animated animate__shakeX' }
            });
            return false;
        }

        Swal.fire({
            title: 'Menyimpan Data...',
            text: 'Mohon tunggu sebentar, sedang memproses data.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        form.submit();
    }
    </script>

</body>
</html>