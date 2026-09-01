<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

</body>
</html>