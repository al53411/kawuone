@extends('layouts.guru')

@section('page_title', 'Dashboard Utama')
@php
$hour = date('H');
$salam = $hour < 11 ? 'Pagi' : ($hour < 15 ? 'Siang' : ($hour < 19 ? 'Sore' : 'Malam' )); @endphp @section('content')
    <!-- Header Konten Dinamis -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Selamat Datang, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-500 mt-1">
            Kelola seluruh data dan aktivitas aktif di
            {{ Auth::user()?->sekolah?->nama_sekolah ?? $profilSekolah?->nama_sekolah ?? 'Sekolah' }}.
        </p>
    </div>



    <!-- Grid Kartu Informasi (Ringkasan Dinamis) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Kartu Jurnal Hari Ini -->
        <div class="glass-card p-6 rounded-2xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Jurnal Hari Ini</h3>
                @if($jurnalHariIni)
                <span class="px-2.5 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                    {{ ucfirst($jurnalHariIni->status ?? 'Terisi') }}
                </span>
                @else
                <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                    Pending
                </span>
                @endif
            </div>

            <p class="text-2xl font-bold text-slate-800">
                {{ $jurnalHariIni ? 'Sudah Mengisi' : 'Belum Mengisi' }}
            </p>

            <p class="text-xs text-slate-400 mt-2">
                @if($jurnalHariIni)
                Diisi pada {{ $jurnalHariIni->created_at->format('H:i') }} WIB
                @else
                Segera isi jurnal mengajar setelah kelas selesai.
                @endif
            </p>
        </div>

        <!-- Kartu Presensi SiHadir -->
        <div class="glass-card p-6 rounded-2xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Presensi SiHadir</h3>
                @if($presensiHariIni)
                <span class="px-2.5 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                    {{ ucfirst($presensiHariIni->status ?? 'Hadir') }}
                </span>
                @else
                <span class="px-2.5 py-1 text-xs font-semibold bg-rose-100 text-rose-800 rounded-full">
                    Belum Absen
                </span>
                @endif
            </div>

            <p class="text-2xl font-bold text-slate-800">
                {{ $presensiHariIni ? \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') . ' WIB' : '--:--' }}
            </p>

            <p class="text-xs text-slate-400 mt-2">
                {{ $presensiHariIni ? 'Tervalidasi via Geofencing & Face Recognition.' : 'Silakan melakukan presensi di aplikasi SiHadir.' }}
            </p>
        </div>

        <!-- Kartu Bank Soal CBT -->
        <div class="glass-card p-6 rounded-2xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Total Soal CBT</h3>
                <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded-full">Aktif</span>
            </div>

            <p class="text-2xl font-bold text-slate-800">
                {{ $totalSoal ?? 0 }} Soal
            </p>

            <p class="text-xs text-slate-400 mt-2">
                Terdaftar di database sistem CBT.
            </p>
        </div>
    </div>

    <!-- Area Informasi / Pengumuman / Aktivitas Terbaru Dinamis -->
    <div class="glass-card p-6 rounded-2xl shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Aktivitas Jurnal Terbaru</h3>

        <div class="border-l-2 border-indigo-500 pl-4 space-y-4">
            @forelse($aktivitasTerbaru as $jurnal)
            <div>
                <span class="text-xs font-semibold text-indigo-600 block">
                    {{ $jurnal->created_at->translatedFormat('d F Y, H:i') }}
                </span>
                <p class="text-sm text-slate-700 font-medium">
                    Jurnal Mengajar {{ $jurnal->kelas->nama_kelas ?? '' }} ({{ $jurnal->mapel ?? 'Mata Pelajaran' }})
                    status: <strong class="capitalize">{{ $jurnal->status ?? 'Terkirim' }}</strong>.
                </p>
            </div>
            @empty
            <div>
                <p class="text-sm text-slate-500 italic">Belum ada riwayat aktivitas jurnal mengajar.</p>
            </div>
            @endforelse
        </div>
    </div>
    @endsection