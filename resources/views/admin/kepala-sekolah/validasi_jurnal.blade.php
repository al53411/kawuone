@extends('layouts.admin')

@section('title', 'Validasi Jurnal')
@section('page_title', 'Persetujuan Jurnal Mengajar')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Validasi Jurnal Mengajar Guru</h1>
    <p class="text-gray-500">Periksa dan validasi laporan jurnal mengajar harian guru pendidik.</p>
</div>

<!-- Alert Sukses -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 text-sm rounded">
    <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
</div>
@endif

<!-- Filter Status Validasi -->
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-2">
        <a href="{{ route('admin.kepala-sekolah.jurnal.index', ['status' => 'Pending']) }}"
            class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $status == 'Pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Menunggu Validasi (Pending)
        </a>
        <a href="{{ route('admin.kepala-sekolah.jurnal.index', ['status' => 'Disetujui']) }}"
            class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $status == 'Disetujui' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Telah Disetujui
        </a>
        <a href="{{ route('admin.kepala-sekolah.jurnal.index', ['status' => 'Ditolak']) }}"
            class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $status == 'Ditolak' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Perlu Revisi (Ditolak)
        </a>
    </div>
</div>

<!-- Tabel Jurnal -->
<div class="flat-card overflow-hidden bg-white mb-6 rounded-xl border border-gray-200 shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-48">Guru & Kelas</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-32">Tanggal & Mapel</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Materi Pembelajaran</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-44 text-center">Status</th>
                <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-52 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($jurnals as $jurnal)
            <tr class="hover:bg-gray-50/50 transition align-top">
                <!-- Guru & Kelas (Menggunakan Null Safe Operator) -->
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
                                class="w-full text-left px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded hover:bg-emerald-700 transition flex items-center justify-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> Setujui
                            </button>
                        </form>

                        <!-- Form Penolakan / Catatan -->
                        <button onclick="toggleRejectModal({{ $jurnal->id }})"
                            class="w-full px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 rounded hover:bg-red-100 transition flex items-center justify-center gap-1">
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

<!-- Pagination -->
<div class="mt-4">
    {{ $jurnals->appends(['status' => $status])->links() }}
</div>

<!-- Modal untuk Mengisi Catatan Penolakan/Revisi -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-900">Tolak & Berikan Catatan Revisi</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="status_validasi" value="Ditolak">
            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Kepala Sekolah (Wajib jika ditolak)</label>
                <textarea name="catatan_kepsek" required rows="4"
                    placeholder="Contoh: Lampiran RPP belum sesuai atau penjelasan materi kurang lengkap..."
                    class="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-red-600 text-white rounded hover:bg-red-700 transition">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRejectModal(jurnalId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    
    // Generasi URL aksi form menggunakan helper Laravel secara langsung
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