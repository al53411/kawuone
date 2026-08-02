@extends('layouts.admin')

@section('title', 'Data Guru')
@section('page_title', 'Guru')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Data Guru</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola seluruh data guru aktif di
            {{ $profilSekolah->nama_sekolah ?? 'Sekolah' }}.</p>
    </div>
    <a href="{{ route('admin.guru.create') }}"
        class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition space-x-2">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Tambah Guru</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center w-12">
                        No</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Identitas (NIK &
                        NIP)</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Guru</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Status & Gol.
                    </th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Jabatan</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-center w-32">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($gurus as $index => $guru)
                <tr class="hover:bg-gray-50/60 transition">
                    <!-- Nomor -->
                    <td class="px-6 py-4 text-sm text-gray-500 text-center font-medium">
                        {{ $loop->iteration }}
                    </td>

                    <!-- NIK & NIP -->
                    <td class="px-6 py-4 text-sm">
                        <div class="font-semibold text-gray-800">
                            {{ $guru->nip ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            NIK: {{ $guru->nik ?? '-' }}
                        </div>
                    </td>

                    <!-- Nama Lengkap & Pendidikan -->
                    <td class="px-6 py-4 text-sm">
                        <div class="font-bold text-gray-900">{{ $guru->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            L/P: {{ $guru->jenis_kelamin ?? '-' }} | Pend: {{ $guru->pendidikan_terakhir ?? '-' }}
                        </div>
                    </td>

                    <!-- Status Kepegawaian & Golongan -->
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center space-x-2">
                            @php
                            $badgeColor = match($guru->status_kepegawaian) {
                            'PNS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'PPPK' => 'bg-blue-50 text-blue-700 border-blue-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-semibold rounded border {{ $badgeColor }}">
                                {{ $guru->status_kepegawaian ?? 'PPPK' }}
                            </span>

                            @if($guru->golongan)
                            <span
                                class="px-2 py-0.5 bg-gray-100 border border-gray-300 text-gray-700 text-xs font-bold rounded">
                                {{ $guru->golongan }}
                            </span>
                            @endif
                        </div>
                    </td>

                    <!-- Jabatan -->
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $guru->jabatan ?? '-' }}
                    </td>

                    <!-- Tombol Aksi -->
                    <td class="px-6 py-4 text-sm text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <!-- Edit -->
                            <a href="{{ route('admin.guru.edit', $guru->id) }}"
                                class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-amber-600 hover:bg-amber-50 hover:border-amber-300 transition"
                                title="Edit Data">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>

                            <!-- Delete -->
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
</div>
@endsection