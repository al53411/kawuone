<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalGuru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class JurnalController extends Controller
{
    /**
     * Menampilkan daftar jurnal mengajar guru & form input
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Cari profil guru yang terikat dengan user yang login
        $guru = $user->guru;
        if (!$guru && class_exists(\App\Models\Guru::class)) {
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
        }

        $sekolahId = $user->sekolah_id ?? $guru?->sekolah_id;

        // Query Dasar Jurnal Guru
        $query = JurnalGuru::with('kelas')->where('guru_id', $user->id);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mapel')) {
            $query->where('mapel', $request->mapel);
        }

        $jurnals = $query->latest('tanggal')->latest('id')->get();

        // 2. PENYARINGAN KELAS DINAMIS (Wali Kelas vs Guru Mapel)
        $userId = $user->id;
        $guruTableId = $guru ? $guru->id : null;

        $kelasWali = Kelas::where(function($q) use ($userId, $guruTableId) {
                $q->where('guru_id', $userId);
                if ($guruTableId) {
                    $q->orWhere('guru_id', $guruTableId);
                }
            })
            ->when($sekolahId && Schema::hasColumn('kelas', 'sekolah_id'), function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            })
            ->orderBy('nama_kelas', 'asc')
            ->get();

        if ($kelasWali->isNotEmpty()) {
            $kelases = $kelasWali;
        } else {
            if ($guru && method_exists($guru, 'kelas') && $guru->kelas()->exists()) {
                $kelases = $guru->kelas()->orderBy('nama_kelas', 'asc')->get();
            } else {
                $kelases = Kelas::when($sekolahId && Schema::hasColumn('kelas', 'sekolah_id'), function ($q) use ($sekolahId) {
                        $q->where('sekolah_id', $sekolahId);
                    })
                    ->orderBy('nama_kelas', 'asc')
                    ->get();
            }
        }

        // 3. PENYARINGAN MAPEL BERDASARKAN RELASI (Eager load relasi kelases)
        $kelasIds = $kelases->pluck('id')->filter();

        if ($guru && method_exists($guru, 'mapels') && $guru->mapels()->exists()) {
            $mapels = $guru->mapels()->with('kelases')->orderBy('nama_mapel', 'asc')->get();
        } else {
            // Ambil seluruh mapel di sekolah yang sama agar mapel lintas kelas (seperti PAI) tetap terbawa
            $mapels = Mapel::with('kelases')
                ->when($sekolahId, function($q) use ($sekolahId) {
                    $q->where('sekolah_id', $sekolahId);
                })->orderBy('nama_mapel', 'asc')->get();
        }

        // Hapus duplikat nama mapel jika di database terlanjur ada data ganda
        $mapels = $mapels->unique('nama_mapel')->values();

        // 4. Ambil Tujuan Pembelajaran Terfilter Saja
        $mapelIdsFinal = $mapels->pluck('id');
        $tujuanPembelajarans = TujuanPembelajaran::with(['mapel', 'kelas'])
            ->whereIn('kelas_id', $kelasIds)
            ->whereIn('mapel_id', $mapelIdsFinal)
            ->get();

        return view('guru.jurnal.index', compact('jurnals', 'kelases', 'mapels', 'tujuanPembelajarans'));
    }

    /**
     * Menyimpan jurnal mengajar baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'kelas_id'   => 'required|exists:kelas,id',
            'mapel'      => 'required|string', 
            'jam_ke'     => 'required|string|max:255',
            'materi'     => 'nullable|string', 
            'select_tp'  => 'nullable|string',
            'kegiatan'   => 'required|string',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $isiMateri = $request->filled('materi') ? $request->materi : $request->select_tp;

        if (empty(trim($isiMateri))) {
            return redirect()->back()->with('error', 'Silakan pilih TP dari dropdown atau ketikkan materi pembelajaran.');
        }

        Carbon::setLocale('id');
        $hari = Carbon::parse($request->tanggal)->translatedFormat('l');

        JurnalGuru::create([
            'guru_id'         => Auth::id(),
            'sekolah_id'      => Auth::user()->sekolah_id ?? Auth::user()->guru?->sekolah_id,
            'kelas_id'        => $request->kelas_id,
            'hari'            => $hari,
            'tanggal'         => $request->tanggal,
            'jam_ke'          => $request->jam_ke,
            'mapel'           => $request->mapel,
            'materi'          => $isiMateri, 
            'kegiatan'        => $request->kegiatan,
            'keterangan'      => $request->keterangan,
            'status_validasi' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Jurnal pembelajaran berhasil disimpan!');
    }

    /**
     * Menghapus jurnal milik guru (jika status masih Pending)
     */
    public function destroy($id)
    {
        $jurnal = JurnalGuru::where('guru_id', Auth::id())->findOrFail($id);

        if ($jurnal->status_validasi === 'Disetujui') {
            return redirect()->back()->with('error', 'Jurnal yang sudah disetujui Kepala Sekolah tidak dapat dihapus.');
        }

        $jurnal->delete();

        return redirect()->back()->with('success', 'Jurnal pembelajaran berhasil dihapus.');
    }

    /**
     * Cetak rekap jurnal guru berdasarkan FILTER (Bulan, Tahun, Kelas, Mapel)
     */
    public function cetakWord(Request $request)
    {
        $user = Auth::user();

        $query = JurnalGuru::with('kelas')->where('guru_id', $user->id);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mapel')) {
            $query->where('mapel', $request->mapel);
        }

        $jurnals = $query->orderBy('tanggal', 'asc')->get();

        if ($jurnals->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data jurnal yang ditemukan sesuai filter untuk dicetak.');
        }

        $adaBelumDisetujui = $jurnals->contains(function ($item) {
            return strtolower($item->status_validasi) !== 'disetujui';
        });

        if ($adaBelumDisetujui) {
            return redirect()->back()->with('error', 'Gagal Download! Masih ada jurnal yang dalam status Pending atau Ditolak oleh Kepala Sekolah.');
        }

        $kepalaSekolah = User::whereIn('role', ['kepala_sekolah', 'kepsek'])->first();
        $templatePath = public_path('templates/template_rekap.docx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template Word tidak ditemukan di: ' . $templatePath);
        }

        try {
            $template = new TemplateProcessor($templatePath);
            $jurnalPertama = $jurnals->first();
            Carbon::setLocale('id');

            $clean = function ($text) {
                return htmlspecialchars($text ?? '-', ENT_QUOTES, 'UTF-8');
            };

            $namaBulan = $request->filled('bulan') 
                ? Carbon::createFromDate($request->tahun ?? date('Y'), $request->bulan, 1)->translatedFormat('F Y')
                : Carbon::parse($jurnalPertama->tanggal)->translatedFormat('F Y');

            $template->setValue('mapel_atas', $clean($request->mapel ?? $jurnalPertama->mapel));
            $template->setValue('kelas_atas', $clean($jurnalPertama->kelas->nama_kelas ?? '-'));
            $template->setValue('bulan', $clean($namaBulan));
            $template->setValue('nama_guru', $clean($user->name));
            $template->setValue('nip', $clean($user->nip));
            $template->setValue('nama_ks', $clean($kepalaSekolah->name ?? '..................................'));
            $template->setValue('nip_ks', $clean($kepalaSekolah->nip ?? '..................................'));

            $groupedJurnals = $jurnals->groupBy('tanggal');
            $totalTanggal = $groupedJurnals->count();

            $template->cloneRow('no', $totalTanggal);

            $i = 1;
            foreach ($groupedJurnals as $tanggal => $items) {
                $tglFormatted = Carbon::parse($tanggal)->translatedFormat('d M Y');
                $hari = $clean($items->first()->hari);

                $jamList        = [];
                $materiList     = [];
                $kegiatanList   = [];
                $keteranganList = [];

                $pakeBullet = count($items) > 1;

                foreach ($items as $item) {
                    $prefix = $pakeBullet ? '● ' : '';

                    $jamList[]        = $prefix . "Jam {$clean($item->jam_ke)} ({$clean($item->kelas->nama_kelas ?? '-')})";
                    $materiList[]     = $prefix . $clean($item->materi);
                    $kegiatanList[]   = $prefix . $clean($item->kegiatan);
                    $keteranganList[] = $prefix . $clean($item->keterangan);
                }

                $template->setValue("no#{$i}", $i);
                $template->setValue("hari#{$i}", $hari);
                $template->setValue("tanggal#{$i}", $clean($tglFormatted));
                $template->setValue("jam_ke#{$i}", implode("\n", $jamList));
                $template->setValue("materi#{$i}", implode("\n", $materiList));
                $template->setValue("kegiatan#{$i}", implode("\n", $kegiatanList));
                $template->setValue("keterangan#{$i}", implode("\n", $keteranganList));

                $i++;
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'rekap_jurnal_') . '.docx';
            $template->saveAs($tempFile);

            while (ob_get_level()) {
                ob_end_clean();
            }

            $safeName = preg_replace('/[^A-Za-z0-9\-]/', '_', $user->name);
            $downloadName = 'Rekap_Jurnal_' . $safeName . '.docx';

            return response()->download($tempFile, $downloadName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses template Word: ' . $e->getMessage());
        }
    }
}