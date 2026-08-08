@extends('layouts.admin')

@section('title', 'Data Guru')
@section('page_title', 'Guru')

@section('content')
<!-- Header & Tombol Tambah -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Data Guru</h1>
        <p class="text-gray-500 text-sm mt-1">
            Kelola seluruh data guru aktif di
            {{ Auth::user()?->sekolah?->nama_sekolah ?? $profilSekolah?->nama_sekolah ?? 'Sekolah' }}.
        </p>
    </div>
    <a href="{{ route('admin.guru.create') }}"
        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition space-x-2">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Tambah Guru</span>
    </a>
</div>

<!-- Alert Notifikasi Flash Session -->
@if(session('success'))
<div
    class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <i class="fa-solid fa-circle-exclamation text-red-600 text-lg"></i>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif

<!-- Bar Pencarian & Filter -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" action="{{ route('admin.guru.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari berdasarkan nama, NIP, atau NIK..."
                class="w-full pl-9 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="w-full md:w-48">
            <select name="status" onchange="this.form.submit()"
                class="w-full border rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="PNS" {{ request('status') == 'PNS' ? 'selected' : '' }}>PNS</option>
                <option value="PPPK" {{ request('status') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                <option value="GTT" {{ request('status') == 'GTT' ? 'selected' : '' }}>GTT / Honorer</option>
            </select>
        </div>

        @if(request('search') || request('status'))
        <a href="{{ route('admin.guru.index') }}"
            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm text-center">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- CONTAINER UTAMA DATA GURU -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">

    <!-- 📱 TAMPILAN MOBILE (CARD VIEW) - Muncul HANYA di Layar HP (< 640px) -->
    <div class="block sm:hidden divide-y divide-gray-200">
        @forelse($gurus as $guru)
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">{{ $guru->nama_lengkap }}</h3>
                    <p class="text-xs text-gray-500">L/P: {{ $guru->jenis_kelamin ?? '-' }} • Pend:
                        {{ $guru->pendidikan_terakhir ?? '-' }}</p>
                </div>
                <!-- Badge Status Kepegawaian -->
                @php
                $badgeColor = match(strtoupper($guru->status_kepegawaian ?? '')) {
                'PNS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'PPPK' => 'bg-blue-50 text-blue-700 border-blue-200',
                default => 'bg-amber-50 text-amber-700 border-amber-200',
                };
                @endphp
                <span class="px-2 py-0.5 text-xs font-semibold rounded border {{ $badgeColor }}">
                    {{ $guru->status_kepegawaian ?? 'GTT' }}
                </span>
            </div>

            <!-- NIP & NIK -->
            <div class="bg-gray-50 p-2.5 rounded-lg text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-500">NIP:</span>
                    <span class="font-semibold text-gray-800">{{ $guru->nip ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">NIK:</span>
                    <span class="font-semibold text-gray-800">{{ $guru->nik ?? '-' }}</span>
                </div>
            </div>

            <!-- Penugasan / Status Kelas (Notifikasi Merah jika belum set) -->
            <div class="text-xs">
                <span class="text-gray-500 block mb-1">Status Penugasan Kelas:</span>
                @if(isset($guru->has_kelas) && $guru->has_kelas)
                <div class="flex flex-wrap gap-1">
                    @foreach($guru->assigned_kelas as $k)
                    <span
                        class="px-2 py-1 bg-emerald-100 text-emerald-800 font-medium rounded-md text-[11px] inline-flex items-center gap-1">
                        <i class="fa-solid fa-chalkboard-user"></i> Wali Kelas {{ $k->nama_kelas }}
                    </span>
                    @endforeach
                </div>
                @else
                <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 border border-red-200 text-red-700 font-semibold rounded-md text-[11px]">
                    <i class="fa-solid fa-triangle-exclamation"></i> Belum Setting Kelas
                </span>
                @endif
            </div>

            <!-- Jabatan & Kontak -->
            <div class="text-xs text-gray-600 flex flex-wrap gap-3">
                @if($guru->jabatan)<span><i class="fa-solid fa-briefcase text-gray-400"></i>
                    {{ $guru->jabatan }}</span>@endif
                @if(isset($guru->mata_pelajaran))<span><i class="fa-solid fa-book text-gray-400"></i>
                    {{ $guru->mata_pelajaran }}</span>@endif
            </div>

            <!-- Akses Tombol -->
            <div class="flex items-center justify-end space-x-2 pt-2 border-t">
                <a href="{{ route('admin.guru.edit', $guru->id) }}"
                    class="px-3 py-1.5 bg-amber-50 border border-amber-300 text-amber-700 rounded-lg text-xs font-medium flex items-center gap-1">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru {{ $guru->nama_lengkap }}?')"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-3 py-1.5 bg-red-50 border border-red-300 text-red-700 rounded-lg text-xs font-medium flex items-center gap-1">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400">
            <i class="fa-solid fa-user-slash text-3xl mb-2"></i>
            <p class="text-sm">Belum ada data guru yang terdaftar.</p>
        </div>
        @endforelse
    </div>

    <!-- 💻 TAMPILAN DESKTOP (TABLE VIEW) - Muncul di Layar Tablet & Laptop (>= 640px) -->
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center w-12">
                        No</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama & Kontak
                    </th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Identitas (NIP /
                        NIK)</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Kepegawaian</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Tugas / Wali
                        Kelas</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center w-32">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($gurus as $guru)
                <tr class="hover:bg-gray-50/60 transition">
                    <!-- Nomor -->
                    <td class="px-6 py-4 text-sm text-gray-500 text-center font-medium">
                        {{ method_exists($gurus, 'firstItem') ? $gurus->firstItem() + $loop->index : $loop->iteration }}
                    </td>

                    <!-- Nama Lengkap & Kontak -->
                    <td class="px-6 py-4 text-sm">
                        <div class="font-bold text-gray-900">{{ $guru->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 mt-0.5 space-x-1">
                            <span>L/P: {{ $guru->jenis_kelamin ?? '-' }}</span>
                            <span>•</span>
                            <span>Pend: {{ $guru->pendidikan_terakhir ?? '-' }}</span>
                        </div>
                        @if($guru->email || $guru->no_hp)
                        <div class="text-xs text-gray-400 mt-1 flex items-center gap-2">
                            @if($guru->no_hp)<span><i class="fa-solid fa-phone text-[10px]"></i>
                                {{ $guru->no_hp }}</span>@endif
                            @if($guru->email)<span><i class="fa-solid fa-envelope text-[10px]"></i>
                                {{ $guru->email }}</span>@endif
                        </div>
                        @endif
                    </td>

                    <!-- NIP & NIK -->
                    <td class="px-6 py-4 text-sm">
                        <div class="font-semibold text-gray-800">
                            NIP: {{ $guru->nip ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            NIK: {{ $guru->nik ?? '-' }}
                        </div>
                    </td>

                    <!-- Status Kepegawaian & Golongan -->
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center space-x-2">
                            @php
                            $badgeColor = match(strtoupper($guru->status_kepegawaian ?? '')) {
                            'PNS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'PPPK' => 'bg-blue-50 text-blue-700 border-blue-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-semibold rounded border {{ $badgeColor }}">
                                {{ $guru->status_kepegawaian ?? 'GTT/Honorer' }}
                            </span>

                            @if($guru->golongan)
                            <span
                                class="px-2 py-0.5 bg-gray-100 border border-gray-300 text-gray-700 text-xs font-bold rounded">
                                {{ $guru->golongan }}
                            </span>
                            @endif
                        </div>
                    </td>

                    <!-- Status Penugasan / Kelas (Indikator Warna) -->
                    <td class="px-6 py-4 text-sm">
                        @if($guru->tipe_penugasan === 'guru_mapel')
                        {{-- Guru Mata Pelajaran --}}
                        <span
                            class="px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 font-semibold rounded-md text-xs inline-flex items-center gap-1">
                            <i class="fa-solid fa-book-open text-blue-600"></i> Guru Mapel (Semua Kelas)
                        </span>
                        @elseif($guru->tipe_penugasan === 'wali_kelas')
                        {{-- Wali Kelas yang Sudah Memiliki Kelas --}}
                        <div class="flex flex-wrap gap-1">
                            @foreach($guru->assigned_kelas as $k)
                            <span
                                class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold rounded-md text-xs inline-flex items-center gap-1">
                                <i class="fa-solid fa-chalkboard-user text-emerald-600"></i> Wali Kelas
                                {{ $k->nama_kelas }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        {{-- Guru Belum Setting Kelas (Merah) --}}
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 text-red-600 font-semibold rounded-md text-xs">
                            <i class="fa-solid fa-triangle-exclamation text-red-500"></i> Belum Setting Kelas
                        </span>
                        @endif
                    </td>

                    <!-- Tombol Aksi -->
                    <td class="px-6 py-4 text-sm text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <a href="{{ route('admin.guru.edit', $guru->id) }}"
                                class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-amber-600 hover:bg-amber-50 hover:border-amber-300 transition"
                                title="Edit Data">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>

                            <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru {{ $guru->nama_lengkap }}?')"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-red-600 hover:bg-red-50 hover:border-red-300 transition"
                                    title="Hapus Data">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fa-solid fa-user-slash text-3xl text-gray-300"></i>
                            <span class="text-sm">Belum ada data guru yang terdaftar.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    @if(method_exists($gurus, 'links') && $gurus->hasPages())
    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-200">
        {{ $gurus->links() }}
    </div>
    @endif
</div>
@endsection