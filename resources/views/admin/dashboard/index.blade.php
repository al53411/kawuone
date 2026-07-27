@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Utama')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Selamat Datang Kembali, Admin!</h1>
        <p class="text-gray-500">Kelola seluruh data siswa aktif {{ $profilSekolah->nama_sekolah }}.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Total Siswa</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $total_siswa ?? 0 }}</h3>
                <p class="text-xs text-green-600 font-medium"><i class="fa-solid fa-arrow-trend-up mr-1"></i>Siswa aktif</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Total Guru</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $total_guru ?? 0 }}</h3>
                <p class="text-xs text-slate-500 font-medium">Tenaga pendidik</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl">
                <i class="fa-solid fa-chalkboard-teacher"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Total Kelas</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $total_kelas ?? 6 }}</h3>
                <p class="text-xs text-slate-500 font-medium">Rombongan belajar</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl">
                <i class="fa-solid fa-school"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Status Sistem</p>
                <h3 class="text-xl font-bold text-green-600 flex items-center gap-2 mt-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span> Normal
                </h3>
                <p class="text-xs text-gray-400 font-medium">Semua modul aman</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600 text-xl">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                <h2 class="text-lg font-bold text-gray-800">Akses Cepat Fitur</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.siswa.index') }}" class="p-4 border rounded-xl hover:bg-blue-50/50 hover:border-blue-200 transition group flex items-start space-x-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition"><i class="fa-solid fa-users text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">Kelola Siswa</h4>
                        <p class="text-xs text-gray-400 mt-1">Tambah, edit, atau mutasi data siswa aktif.</p>
                    </div>
                </a>
                <a href="{{ route('admin.guru.index') }}" class="p-4 border rounded-xl hover:bg-indigo-50/50 hover:border-indigo-200 transition group flex items-start space-x-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition"><i class="fa-solid fa-id-card text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">Kelola Guru</h4>
                        <p class="text-xs text-gray-400 mt-1">Atur data pendidik, jabatan, dan golongan.</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-4 mb-4">Aktivitas Sistem</h2>
            <div class="space-y-4">
                <div class="flex items-start space-x-3 text-sm">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></div>
                    <div>
                        <p class="text-gray-700 font-medium">Data Siswa baru ditambahkan</p>
                        <span class="text-xs text-gray-400">Baru saja</span>
                    </div>
                </div>
                <div class="flex items-start space-x-3 text-sm">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                    <div>
                        <p class="text-gray-700 font-medium">Database kelas berhasil di-seed</p>
                        <span class="text-xs text-gray-400">10 menit yang lalu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection