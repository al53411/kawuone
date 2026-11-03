@extends('layouts.superadmin')

@section('title', 'Pratinjau Ekstraksi Capaian Pembelajaran')

@section('content')
    <div class="w-full bg-white p-6 shadow-sm border border-slate-200 rounded-xl mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Pratinjau Hasil Ekstraksi CP</h1>
                <p class="text-xs text-slate-500 mt-0.5">Periksa kembali data di bawah ini sebelum menyimpannya ke database.
                    Anda dapat mengubah teks jika diperlukan.</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg text-xs font-bold text-emerald-800">
                Mata Pelajaran: {{ $mapel->nama_mapel }}
            </div>
        </div>

        <form action="{{ route('superadmin.capaian-pembelajaran.store-batch') }}" method="POST">
            @csrf
            <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">

            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm mb-6">
                <table class="w-full text-sm text-left border-collapse border border-slate-200">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-100 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 border border-slate-200 w-28 text-center">FASE</th>
                            <th class="px-4 py-3 border border-slate-200 w-52">ELEMEN</th>
                            <th class="px-4 py-3 border border-slate-200">DESKRIPSI CAPAIAN PEMBELAJARAN (CP)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($parsedData as $index => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 border border-slate-200 text-center">
                                    <select name="fase[]"
                                        class="text-xs font-bold rounded-lg border-slate-300 py-1 px-2 bg-white">
                                        @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $f)
                                            <option value="{{ $f }}" {{ ($item['fase'] ?? $selectedFase) == $f ? 'selected' : '' }}>
                                                Fase {{ $f }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 border border-slate-200">
                                    <input type="text" name="elemen[]" value="{{ $item['elemen'] }}" required
                                        class="w-full text-xs font-semibold rounded-lg border-slate-300 p-2 bg-white">
                                </td>
                                <td class="px-4 py-3 border border-slate-200">
                                    <textarea name="deskripsi_cp[]" rows="3" required
                                        class="w-full text-xs rounded-lg border-slate-300 p-2 bg-white leading-relaxed">{{ $item['deskripsi_cp'] }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.capaian-pembelajaran.index') }}"
                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm rounded-xl transition">
                    Batal / Batalkan
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Konfirmasi & Simpan Permanen
                </button>
            </div>
        </form>
    </div>
@endsection