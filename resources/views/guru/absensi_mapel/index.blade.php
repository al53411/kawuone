@extends('layouts.guru')

@section('title', 'Absensi Mata Pelajaran')

@section('content')
    <div class="w-full bg-white text-slate-800 p-4 sm:p-6">

        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Absensi Mata Pelajaran</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catat kehadiran siswa harian sesuai kelas dan mata
                    pelajaran.</p>
            </div>
            <div>
                <a href="{{ route('guru.absensi-mapel.rekap') }}"
                    class="w-full sm:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-900 active:bg-black text-white font-semibold rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm">
                    <span>📊</span> Lihat Rekap Bulanan
                </a>
            </div>
        </div>

        <!-- Alert Notifikasi -->
        @if(session('success'))
            <div
                class="mb-4 p-3.5 sm:p-4 text-xs sm:text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div
                class="mb-4 p-3.5 sm:p-4 text-xs sm:text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-2">
                <span class="text-base">🚫</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Kelas, Mapel, Pertemuan, & Tanggal -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 mb-6">
            <form action="{{ route('guru.absensi-mapel.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 items-end">

                <!-- Pilih Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Kelas</label>
                    <select name="kelas_id" required
                        class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Mata Pelajaran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Pelajaran</label>
                    <select name="mapel_id" required
                        class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ $mapelId == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pertemuan Ke- -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pertemuan Ke- (Minggu)</label>
                    <select name="pertemuan_ke" required
                        class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                        @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}" {{ $pertemuanKe == $i ? 'selected' : '' }}>
                                Pertemuan {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" required
                        class="w-full text-base sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                </div>

                <!-- Tombol Filter -->
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        <span>🔍</span> Tampilkan
                    </button>
                </div>
            </form>
        </div>

        @if(isset($siswas) && count($siswas) > 0)

            <!-- Ringkasan Statistik Kehadiran -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bg-emerald-50 border border-emerald-100 p-3.5 sm:p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-emerald-600">Hadir</p>
                        <p class="text-lg sm:text-xl font-bold text-emerald-800 mt-0.5" id="stat-hadir">0</p>
                    </div>
                    <span class="text-xl sm:text-2xl">🟢</span>
                </div>
                <div class="bg-blue-50 border border-blue-100 p-3.5 sm:p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-blue-600">Izin</p>
                        <p class="text-lg sm:text-xl font-bold text-blue-800 mt-0.5" id="stat-izin">0</p>
                    </div>
                    <span class="text-xl sm:text-2xl">🔵</span>
                </div>
                <div class="bg-amber-50 border border-amber-100 p-3.5 sm:p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-amber-600">Sakit</p>
                        <p class="text-lg sm:text-xl font-bold text-amber-800 mt-0.5" id="stat-sakit">0</p>
                    </div>
                    <span class="text-xl sm:text-2xl">🟡</span>
                </div>
                <div class="bg-rose-50 border border-rose-100 p-3.5 sm:p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-rose-600">Alpa</p>
                        <p class="text-lg sm:text-xl font-bold text-rose-800 mt-0.5" id="stat-alpa">0</p>
                    </div>
                    <span class="text-xl sm:text-2xl">🔴</span>
                </div>
            </div>

            <!-- Form Simpan Absensi Mapel -->
            <form action="{{ route('guru.absensi-mapel.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <input type="hidden" name="mapel_id" value="{{ $mapelId }}">
                <input type="hidden" name="pertemuan_ke" value="{{ $pertemuanKe }}">

                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span>📋</span> Daftar Siswa ({{ count($siswas) }} Anak)
                        </h2>

                        <!-- Quick Set All Hadir -->
                        <button type="button" onclick="setAllHadir()"
                            class="text-xs bg-emerald-100 text-emerald-800 hover:bg-emerald-200 font-semibold px-3 py-1.5 rounded-lg transition">
                            ⚡ Tandai Semua Hadir
                        </button>
                    </div>

                    <!-- TAMPILAN MOBILE (Card View) -->
                    <div class="block md:hidden space-y-3.5">
                        @foreach($siswas as $index => $siswa)
                            @php
                                $statusAwal = $absensiExisting[$siswa->id]->status ?? 'Hadir';
                                $ketAwal = $absensiExisting[$siswa->id]->keterangan ?? '';
                                $namaSiswa = $siswa->nama_lengkap ?? $siswa->nama ?? $siswa->nama_siswa ?? '-';
                            @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs font-bold text-slate-400">#{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-bold text-sm text-slate-800">{{ $namaSiswa }}</p>
                                            <p class="text-[11px] text-slate-500">NISN: {{ $siswa->nisn ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Options (Mobile) -->
                                <div class="grid grid-cols-4 gap-1.5 text-center">
                                    @foreach(['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st)
                                        <label class="cursor-pointer">
                                            <input type="radio" value="{{ $st }}" {{ $statusAwal === $st ? 'checked' : '' }}
                                                class="peer hidden status-radio mobile-radio-{{ $siswa->id }}"
                                                onchange="syncToDesk({{ $siswa->id }}, '{{ $st }}')">
                                            <div
                                                class="py-1.5 text-xs font-semibold rounded-lg border border-slate-200 
                                                                                                                                                {{ $st == 'Hadir' ? 'peer-checked:bg-emerald-600 peer-checked:border-emerald-600' : '' }}
                                                                                                                                                {{ $st == 'Izin' ? 'peer-checked:bg-blue-600 peer-checked:border-blue-600' : '' }}
                                                                                                                                                {{ $st == 'Sakit' ? 'peer-checked:bg-amber-500 peer-checked:border-amber-500' : '' }}
                                                                                                                                                {{ $st == 'Alpa' ? 'peer-checked:bg-rose-600 peer-checked:border-rose-600' : '' }}
                                                                                                                                                peer-checked:text-white text-slate-600 transition">
                                                {{ $st }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div>
                                    <input type="text" value="{{ $ketAwal }}" placeholder="Catatan (Opsional)"
                                        class="w-full text-xs rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-2.5 ket-input-{{ $siswa->id }}"
                                        oninput="syncKet({{ $siswa->id }}, this.value, 'desk')">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- TAMPILAN DESKTOP (Table View) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">No</th>
                                    <th class="px-4 py-3">Nama Siswa</th>
                                    <th class="px-4 py-3">NISN</th>
                                    <th class="px-4 py-3 text-center w-80">Status Kehadiran</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($siswas as $index => $siswa)
                                    @php
                                        $statusAwal = $absensiExisting[$siswa->id]->status ?? 'Hadir';
                                        $ketAwal = $absensiExisting[$siswa->id]->keterangan ?? '';
                                        $namaSiswa = $siswa->nama_lengkap ?? $siswa->nama ?? $siswa->nama_siswa ?? '-';
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5 text-center font-semibold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-800">{{ $namaSiswa }}</td>
                                        <td class="px-4 py-3.5 text-slate-500 text-xs">{{ $siswa->nisn ?? '-' }}</td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="inline-flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                                                @foreach(['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="absensi[{{ $siswa->id }}][status]" value="{{ $st }}"
                                                            {{ $statusAwal === $st ? 'checked' : '' }}
                                                            class="peer hidden status-radio status-radio-desk desk-radio-{{ $siswa->id }}"
                                                            onchange="syncToMobile({{ $siswa->id }}, '{{ $st }}')">
                                                        <span
                                                            class="px-3 py-1 text-xs font-semibold rounded-lg block 
                                                                                                                                                            {{ $st == 'Hadir' ? 'peer-checked:bg-emerald-600' : '' }}
                                                                                                                                                            {{ $st == 'Izin' ? 'peer-checked:bg-blue-600' : '' }}
                                                                                                                                                            {{ $st == 'Sakit' ? 'peer-checked:bg-amber-500' : '' }}
                                                                                                                                                            {{ $st == 'Alpa' ? 'peer-checked:bg-rose-600' : '' }}
                                                                                                                                                            peer-checked:text-white text-slate-600 transition">
                                                            {{ $st }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <input type="text" name="absensi[{{ $siswa->id }}][keterangan]" value="{{ $ketAwal }}"
                                                placeholder="Alasan izin/sakit..."
                                                class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3 desk-ket-{{ $siswa->id }}"
                                                oninput="syncKet({{ $siswa->id }}, this.value, 'mobile')">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                            <span>💾</span> Simpan Absensi Mapel
                        </button>
                    </div>
                </div>
            </form>

        @else
            <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center space-y-2">
                <span class="text-3xl">📚</span>
                <p class="font-bold text-slate-700">Pilih Kelas & Mata Pelajaran</p>
                <p class="text-xs text-slate-500">Silakan pilih kelas, mata pelajaran, dan pertemuan di atas lalu klik
                    "Tampilkan".</p>
            </div>
        @endif

    </div>

    <!-- JavaScript Sinkronisasi & Statistik -->
    <script>
        function updateStats() {
            let hadir = 0, izin = 0, sakit = 0, alpa = 0;

            document.querySelectorAll('.status-radio-desk:checked').forEach(radio => {
                if (radio.value === 'Hadir') hadir++;
                if (radio.value === 'Izin') izin++;
                if (radio.value === 'Sakit') sakit++;
                if (radio.value === 'Alpa') alpa++;
            });

            document.getElementById('stat-hadir').innerText = hadir;
            document.getElementById('stat-izin').innerText = izin;
            document.getElementById('stat-sakit').innerText = sakit;
            document.getElementById('stat-alpa').innerText = alpa;
        }

        function setAllHadir() {
            document.querySelectorAll('.status-radio[value="Hadir"]').forEach(radio => radio.checked = true);
            document.querySelectorAll('.status-radio-desk[value="Hadir"]').forEach(radio => radio.checked = true);
            updateStats();
        }

        function syncToDesk(siswaId, value) {
            const deskRadio = document.querySelector(`.desk-radio-${siswaId}[value="${value}"]`);
            if (deskRadio) deskRadio.checked = true;
            updateStats();
        }

        function syncToMobile(siswaId, value) {
            const mobileRadio = document.querySelector(`.mobile-radio-${siswaId}[value="${value}"]`);
            if (mobileRadio) mobileRadio.checked = true;
            updateStats();
        }

        function syncKet(siswaId, value, target) {
            if (target === 'desk') {
                const deskInput = document.querySelector(`.desk-ket-${siswaId}`);
                if (deskInput) deskInput.value = value;
            } else {
                const mobileInput = document.querySelector(`.ket-input-${siswaId}`);
                if (mobileInput) mobileInput.value = value;
            }
        }

        document.addEventListener('DOMContentLoaded', updateStats);
    </script>
@endsection