@extends('layouts.superadmin')

@section('title', 'Data Kepala Sekolah')
@section('page_title', 'Data Kepala Sekolah')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Kepala Sekolah</h1>
            <p class="text-slate-500 text-sm">Daftar akun Kepala Sekolah (Admin Sekolah) seluruh unit sekolah.</p>
        </div>
        <a href="{{ route('superadmin.kepsek.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
            <i class="fa-solid fa-user-plus mr-2"></i> Tambah Kepala Sekolah
        </a>
    </div>

    <!-- Tabel Data Flat -->
    <div class="flat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase text-xs font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama Kepala Sekolah</th>
                        <th class="px-6 py-3">Sekolah</th>
                        <th class="px-6 py-3">Username / Email</th>
                        <th class="px-6 py-3 text-center">Status Password</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($kepseks as $index => $item)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $item->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ $item->sekolah->nama_sekolah ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-700">{{ $item->email }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-600">
                                <i class="fa-solid fa-lock text-[10px] mr-1"></i> Terenkripsi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <!-- Tombol Reset Password -->
                            <button onclick="resetPassword('{{ $item->id }}', '{{ $item->name }}')" 
                                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded text-xs font-medium transition inline-flex items-center">
                                <i class="fa-solid fa-key mr-1"></i> Reset Password
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            <i class="fa-solid fa-user-slash text-3xl mb-2 block"></i>
                            Belum ada data kepala sekolah.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection