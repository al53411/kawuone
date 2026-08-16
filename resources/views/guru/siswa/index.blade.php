@extends('layouts.guru')

@section('title', $profilSekolah->nama_sekolah ?? 'SDN Kawu 1')

@section('content')
<!-- Container Full Width dengan Padding Responsif -->
<div class="py-6 w-full px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Title & Stats Header -->
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Daftar Siswa</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola dan lihat data siswa yang terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-blue-50 text-blue-700 text-xs font-medium px-3 py-1.5 rounded-lg border border-blue-100">
                Total: <strong class="font-bold">{{ count($siswas) }}</strong> Siswa
            </span>
        </div>
    </div>

    <!-- Main Container (Full Width) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden w-full">

        <!-- 1. TAMPILAN MOBILE (Card View) - Tampil di Layar Kecil (< sm) -->
        <div class="block sm:hidden divide-y divide-gray-100">
            @forelse ($siswas as $index => $siswa)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600">
                            {{ $index + 1 }}
                        </span>
                        <h2 class="font-bold text-gray-900 text-base">
                            {{ $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? 'Tanpa Nama' }}
                        </h2>
                    </div>
                    <span
                        class="shrink-0 bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-200">
                        {{ $siswa->kelas->nama_kelas ?? '-' }}
                    </span>
                </div>

                <div class="ml-8 space-y-1 text-xs text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6" />
                        </svg>
                        <span>NISN: <strong class="font-mono text-gray-700">{{ $siswa->nisn ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 text-sm">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Belum ada data siswa yang tersedia.
            </div>
            @endforelse
        </div>

        <!-- 2. TAMPILAN DESKTOP/TABLET (Table View) - Tampil di Layar >= sm -->
        <div class="hidden sm:block overflow-x-auto w-full">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-center w-16">No</th>
                        <th scope="col" class="px-6 py-3.5">NISN</th>
                        <th scope="col" class="px-6 py-3.5">Nama Siswa</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($siswas as $index => $siswa)
                    <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-mono text-gray-600">{{ $siswa->nisn ?? '-' }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            {{ $siswa->nama_siswa ?? $siswa->nama_lengkap ?? $siswa->nama ?? 'Tanpa Nama' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full border border-blue-200">
                                {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Belum ada data siswa yang tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection