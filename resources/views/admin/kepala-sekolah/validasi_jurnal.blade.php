@extends('layouts.admin')

@section('title', 'Validasi Jurnal')
@section('page_title', 'Persetujuan Jurnal Mengajar')

@section('content')
<div class="mb-6 md:mb-8">
    <h1 class="text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Validasi Jurnal Mengajar Guru</h1>
    <p class="text-xs md:text-sm text-gray-500">Periksa dan validasi laporan jurnal mengajar harian guru pendidik.</p>
</div>

<!-- Alert Sukses -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 text-sm rounded-lg flex items-center gap-2">
    <i class="fa-solid fa-circle-check text-green-600"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Filter Status Validasi (Scrollable di Mobile) -->
<div class="flex items-center justify-between mb-6 overflow-x-auto pb-2 scrollbar-none">
    <div class="flex gap-2 whitespace-nowrap">
        <a href="{{ route('admin.kepala-sekolah.jurnal.index', ['status' => 'Pending']) }}"
            class="px-3.5 py-2 text-xs font-bold rounded-lg transition shrink-0 {{ $status == 'Pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Menunggu Validasi (Pending)
        </a>
        <a href="{{ route('admin.kepala-sekolah.jurnal.index', ['status' => 'Disetujui']) }}"
            class="px-3.5 py-2 text-xs font-bold rounded-lg transition shrink-0 {{ $status == 'Disetujui' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Telah Disetujui
        </a>
        <a href="{{ route('admin.kepala-sekolah.jurnal.index', ['status' => 'Ditolak']) }}"
            class="px-3.5 py-2 text-xs font-bold rounded-lg transition shrink-0 {{ $status == 'Ditolak' ? 'bg-red-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Perlu Revisi (Ditolak)
        </a>
    </div>
</div>

<!-- CONTAINER UTAMA -->

<!-- 1. TAMPILAN MOBILE CARD (Hanya tampil di Layar < 768px / md) -->
<div class="grid grid-cols-1 gap-4 md:hidden mb-6">
    @forelse($jurnals as $jurnal)
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-3">
        <!-- Header Card: Nama & Status -->
        <div class="flex justify-between items-start gap-2 border-b border-gray-100 pb-3">
            <div>
                <h2 class="font-bold text-gray-900 text-sm">
                    {{ $jurnal->guru?->nama_lengkap ?? $jurnal->guru?->name ?? 'Guru Tidak Ditemukan' }}
                </h2>
                <span class="text-xs text-gray-500 block">Kelas: {{ $jurnal->kelas?->nama_kelas ?? '-' }}</span>
            </div>
            <div>
                @if($jurnal->status_validasi == 'Pending')
                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu</span>
                @elseif($jurnal->status_validasi == 'Disetujui')
                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-emerald-100 text-emerald-800">Disetujui</span>
                @else
                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                @endif
            </div>
        </div>

        <!-- Detail Tanggal & Mapel -->
        <div class="flex items-center justify-between text-xs">
            <span class="font-medium text-gray-700">
                <i class="fa-regular fa-calendar mr-1 text-gray-400"></i>
                {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}
            </span>
            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 font-semibold rounded">
                {{ $jurnal->mapel }}
            </span>
        </div>

        <!-- Detail Materi & Kegiatan -->
        <div class="text-xs space-y-1 bg-gray-50 p-3 rounded-lg border border-gray-100">
            <p class="font-semibold text-gray-800">{{ $jurnal->materi }}</p>
            <p class="text-gray-600 line-clamp-2">Kegiatan: {{ Str::limit($jurnal->kegiatan, 100) }}</p>
            
            @if($jurnal->catatan_kepsek)
            <div class="mt-2 text-xs p-2 bg-amber-50 border-l-2 border-amber-400 rounded text-amber-900">
                <strong>Catatan Kepsek:</strong> "{{ $jurnal->catatan_kepsek }}"
            </div>
            @endif
        </div>

        <!-- Tombol Aksi Mobile -->
        <div class="pt-2">
            @if($jurnal->status_validasi == 'Pending')
            <div class="grid grid-cols-2 gap-2">
                <!-- Form Persetujuan -->
                <form action="{{ route('admin.kepala-sekolah.jurnal.update', $jurnal->id) }}" method="POST" class="w-full">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status_validasi" value="Disetujui">
                    <button type="submit"
                        class="w-full py-2 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-check text-[11px]"></i> Setujui
                    </button>
                </form>

                <!-- Form Penolakan -->
                <button onclick="toggleRejectModal({{ $jurnal->id }})"
                    class="w-full py-2 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-xmark text-[11px]"></i> Beri Catatan
                </button>
            </div>
            @else
            <div class="text-center text-xs text-gray-400 italic">
                Tervalidasi pada {{ $jurnal->tanggal_validasi ? \Carbon\Carbon::parse($jurnal->tanggal_validasi)->translatedFormat('d/m/Y') : '-' }}
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
        <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
        Tidak ada jurnal mengajar dengan status "{{ $status }}".
    </div>
    @endforelse
</div>

<!-- 2. TAMPILAN DESKTOP TABEL (Hanya tampil di Layar >= 768px / md) -->
<div class="hidden md:block flat-card overflow-hidden bg-white mb-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-48">Guru & Kelas</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-32">Tanggal & Mapel</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Materi Pembelajaran</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-36 text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-44 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($jurnals as $jurnal)
                <tr class="hover:bg-gray-50/50 transition align-top">
                    <!-- Guru & Kelas -->
                    <td class="px-6 py-4 text-sm">
                        <span class="font-semibold text-gray-900 block">{{ $jurnal->guru?->nama_lengkap ?? $jurnal->guru?->name ?? 'Guru Tidak Ditemukan' }}</span>
                        <span class="text-xs text-gray-500 block mt-0.5">Kelas: {{ $jurnal->kelas?->nama_kelas ?? '-' }}</span>
                    </td>

                    <!-- Tanggal & Mapel -->
                    <td class="px-6 py-4 text-sm">
                        <span class="font-medium text-gray-800 block">
                            {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}
                        </span>
                        <span class="text-xs text-blue-600 font-semibold block mt-0.5">{{ $jurnal->mapel }}</span>
                    </td>

                    <!-- Laporan Materi -->
                    <td class="px-6 py-4 text-sm">
                        <div class="text-gray-800 font-medium line-clamp-2">{{ $jurnal->materi }}</div>
                        <p class="text-xs text-gray-500 mt-1">Kegiatan: {{ Str::limit($jurnal->kegiatan, 100) }}</p>
                        @if($jurnal->catatan_kepsek)
                        <div class="mt-2 text-xs p-2 bg-amber-50 border-l-2 border-amber-400 rounded text-amber-900">
                            <strong>Catatan Kepsek:</strong> "{{ $jurnal->catatan_kepsek }}"
                        </div>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 text-center">
                        @if($jurnal->status_validasi == 'Pending')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu</span>
                        @elseif($jurnal->status_validasi == 'Disetujui')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Disetujui</span>
                        @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                        @endif
                    </td>

                    <!-- Aksi Validasi Form -->
                    <td class="px-6 py-4 text-right">
                        @if($jurnal->status_validasi == 'Pending')
                        <div class="flex flex-col gap-2 justify-end">
                            <!-- Form Persetujuan -->
                            <form action="{{ route('admin.kepala-sekolah.jurnal.update', $jurnal->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status_validasi" value="Disetujui">
                                <button type="submit"
                                    class="w-full text-left px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-check text-[10px]"></i> Setujui
                                </button>
                            </form>

                            <!-- Form Penolakan / Catatan -->
                            <button onclick="toggleRejectModal({{ $jurnal->id }})"
                                class="w-full px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center justify-center gap-1">
                                <i class="fa-solid fa-xmark text-[10px]"></i> Beri Catatan
                            </button>
                        </div>
                        @else
                        <span class="text-xs text-gray-400 italic">Tervalidasi pada 
                            {{ $jurnal->tanggal_validasi ? \Carbon\Carbon::parse($jurnal->tanggal_validasi)->translatedFormat('d/m/Y') : '-' }}
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                        Tidak ada jurnal mengajar dengan status "{{ $status }}".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $jurnals->appends(['status' => $status])->links() }}
</div>

<!-- Modal Penolakan / Revisi (Mobile Optimized) -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm md:text-base font-bold text-gray-900">Tolak & Berikan Catatan Revisi</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="status_validasi" value="Ditolak">
            <div class="p-5">
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Catatan Kepala Sekolah (Wajib)</label>
                <textarea name="catatan_kepsek" required rows="4"
                    placeholder="Contoh: Lampiran RPP belum sesuai atau penjelasan materi kurang lengkap..."
                    class="w-full p-3 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"></textarea>
            </div>
            <div class="bg-gray-50 px-5 py-3.5 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-xs md:text-sm font-semibold text-gray-600 hover:text-gray-800 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-xs md:text-sm font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm transition">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRejectModal(jurnalId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    
    let baseUrl = "{{ url('admin/kepala-sekolah/jurnal') }}";
    form.action = baseUrl + '/' + jurnalId;

    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    
    modal.classList.add('hidden');
    form.reset(); 
}
</script>
@endsection