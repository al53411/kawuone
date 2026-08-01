@extends('layouts.superadmin')

@section('title', 'Data Sekolah')
@section('page_title', 'Daftar Sekolah')

@section('content')
<!-- Pembungkus dibuat w-full (100% lebar layar area konten) -->
<div class="w-full">

    <!-- Flash Alert Sukses -->
    @if(session('success'))
        <div class="mb-5 p-4 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Card Utama Flat Full Width -->
    <div class="flat-card w-full bg-white border border-slate-200 rounded-md overflow-hidden">
        
        <!-- Header Card Flat & Tombol Tambah -->
        <div class="bg-slate-900 px-6 py-4 border-b border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3 text-white">
                <i class="fa-solid fa-school text-emerald-400 text-lg"></i>
                <div>
                    <h2 class="font-bold text-sm tracking-wide">Kelola Data Sekolah</h2>
                    <p class="text-xs text-slate-400">Daftar seluruh sekolah yang terdaftar dalam sistem</p>
                </div>
            </div>
            
            <a href="{{ route('superadmin.sekolah.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Tambah Sekolah Baru
            </a>
        </div>

        <!-- Filter & Search Bar (Opsional / Siap Pakai) -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row gap-3 justify-between items-center">
            <form action="{{ route('superadmin.sekolah.index') }}" method="GET" class="w-full md:w-auto flex flex-col md:flex-row gap-2">
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NPSN atau Nama..." class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-300 rounded text-xs focus:border-emerald-500 focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-medium rounded transition">
                    Filter
                </button>
                @if(request('search'))
                    <a href="{{ route('superadmin.sekolah.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-medium rounded transition text-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Data Sekolah -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">NPSN</th>
                        <th class="py-3 px-4">Nama Sekolah</th>
                        <th class="py-3 px-4">Jenjang & Status</th>
                        <th class="py-3 px-4">Kepala Sekolah</th>
                        <th class="py-3 px-4">Kecamatan / Kab.</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs text-slate-700">
                    @forelse($sekolah as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 text-center font-medium text-slate-500">
                                {{ method_exists($sekolah, 'firstItem') ? $sekolah->firstItem() + $index : $index + 1 }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                {{ $item->npsn }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                {{ $item->nama_sekolah }}
                                @if($item->email)
                                    <span class="block text-[11px] font-normal text-slate-400">{{ $item->email }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-800">
                                        {{ $item->jenjang }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $item->status == 'Negeri' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $item->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                {{ $item->nama_kepsek ?? '-' }}
                                @if($item->nip_kepsek)
                                    <span class="block text-[10px] text-slate-400">NIP: {{ $item->nip_kepsek }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                {{ $item->kecamatan ?? '-' }}
                                @if($item->kabupaten_kota)
                                    <span class="block text-[10px] text-slate-400">{{ $item->kabupaten_kota }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit -->
                                    <a href="{{ route('superadmin.sekolah.edit', $item->id) }}" title="Edit Data" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded border border-amber-200 transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Hapus -->
                                    <form action="{{ route('superadmin.sekolah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah {{ $item->nama_sekolah }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Sekolah" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded border border-rose-200 transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-school-circle-xmark text-3xl mb-2 block text-slate-300"></i>
                                Belum ada data sekolah yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($sekolah, 'hasPages') && $sekolah->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $sekolah->links() }}
            </div>
        @endif

    </div>
</div>
@endsection