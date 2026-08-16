<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalGuru;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class JurnalController extends Controller
{
    /**
     * Menampilkan daftar jurnal mengajar guru & form input
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil riwayat jurnal mengajar milik guru yang sedang login
        $jurnals = JurnalGuru::with('kelas')
            ->where('guru_id', $user->id)
            ->latest()
            ->get();

        // Ambil data kelas untuk pilihan dropdown
        $kelases = Kelas::all();

        // Daftar Mata Pelajaran untuk dropdown Blade
        $mapels = [
            'Pendidikan Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'IPAS',
            'PJOK',
            'Seni Budaya',
            'PAI & Budi Pekerti',
            'Bahasa Jawa',
            'Bahasa Inggris',
            'Tematik / Guru Kelas',
        ];

        return view('guru.jurnal.index', compact('jurnals', 'kelases', 'mapels'));
    }

    /**
     * Menyimpan jurnal mengajar baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Form
        $request->validate([
            'tanggal'    => 'required|date',
            'kelas_id'   => 'required|exists:kelas,id',
            'mapel'      => 'required|string|max:255',
            'jam_ke'     => 'required|string|max:255',
            'materi'     => 'required|string',
            'kegiatan'   => 'required|string',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'tanggal.required'  => 'Tanggal mengajar wajib diisi.',
            'kelas_id.required' => 'Silakan pilih kelas terlebih dahulu.',
            'mapel.required'    => 'Mata pelajaran wajib diisi.',
            'jam_ke.required'   => 'Jam ke- wajib diisi.',
            'materi.required'   => 'Materi / TP pembelajaran wajib diisi.',
            'kegiatan.required' => 'Kegiatan pembelajaran wajib diisi.',
        ]);

        // 2. Konversi tanggal menjadi nama Hari dalam Bahasa Indonesia
        Carbon::setLocale('id');
        $hari = Carbon::parse($request->tanggal)->translatedFormat('l');

        // 3. Simpan Data ke Tabel jurnal_gurus
        JurnalGuru::create([
            'guru_id'         => Auth::id(),
            'kelas_id'        => $request->kelas_id,
            'hari'            => $hari,
            'tanggal'         => $request->tanggal,
            'jam_ke'          => $request->jam_ke,
            'mapel'           => $request->mapel,
            'materi'          => $request->materi,
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
     * Cetak rekap jurnal guru menggunakan template Word (.docx)
     */
    public function cetakWord(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil data jurnal guru yang sedang login
        $jurnals = JurnalGuru::with('kelas')
            ->where('guru_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($jurnals->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data jurnal untuk dicetak.');
        }

        // 2. KUNCIAN DOWNLOAD: Cek apakah ada jurnal yang belum Disetujui / Ditolak
        $adaBelumDisetujui = $jurnals->contains(function ($item) {
            return strtolower($item->status_validasi) !== 'disetujui';
        });

        if ($adaBelumDisetujui) {
            return redirect()->back()->with('error', 'Gagal Download! Masih ada jurnal yang dalam status Pending atau Ditolak oleh Kepala Sekolah.');
        }

        // 3. Ambil Data Kepala Sekolah
        $kepalaSekolah = User::whereIn('role', ['kepala_sekolah', 'kepsek'])->first();

        // 4. Path File Template Word
        $templatePath = public_path('templates/template_rekap.docx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template Word tidak ditemukan di: ' . $templatePath);
        }

        try {
            $template = new TemplateProcessor($templatePath);

            $jurnalPertama = $jurnals->first();
            Carbon::setLocale('id');

            // Fungsi pembantu untuk membersihkan karakter khusus XML (&, <, >)
            $clean = function ($text) {
                return htmlspecialchars($text ?? '-', ENT_QUOTES, 'UTF-8');
            };

            // 5. Mengisi variabel bagian atas (Header)
            $template->setValue('mapel_atas', $clean($jurnalPertama->mapel));
            $template->setValue('kelas_atas', $clean($jurnalPertama->kelas->nama_kelas ?? null));
            $template->setValue('bulan', Carbon::now()->translatedFormat('F Y'));

            // 6. Tanda tangan & Identitas Guru
            $template->setValue('nama_guru', $clean($user->name));
            $template->setValue('nip', $clean($user->nip));

            // 7. Tanda tangan & Identitas Kepala Sekolah
            $template->setValue('nama_ks', $clean($kepalaSekolah->name ?? '..................................'));
            $template->setValue('nip_ks', $clean($kepalaSekolah->nip ?? '..................................'));

            // 8. GROUPING BERDASARKAN TANGGAL (Menggabungkan tanggal yang sama ke dalam 1 baris)
            $groupedJurnals = $jurnals->groupBy('tanggal');
            $totalTanggal = $groupedJurnals->count();

            // Clone row berdasarkan jumlah TANGGAL UNIK (bukan total baris)
            $template->cloneRow('no', $totalTanggal);

            $i = 1;
            foreach ($groupedJurnals as $tanggal => $items) {
                $tglFormatted = Carbon::parse($tanggal)->translatedFormat('d M Y');
                $hari = $clean($items->first()->hari);

                $jamList        = [];
                $materiList     = [];
                $kegiatanList   = [];
                $keteranganList = [];

                // Jika pada 1 tanggal ada lebih dari 1 jurnal, format dengan simbol '●'
                $pakeBullet = count($items) > 1;

                foreach ($items as $item) {
                    $prefix = $pakeBullet ? '● ' : '';

                    $jamList[]        = $prefix . "Jam {$clean($item->jam_ke)} ({$clean($item->kelas->nama_kelas ?? '-')})";
                    $materiList[]     = $prefix . $clean($item->materi);
                    $kegiatanList[]   = $prefix . $clean($item->kegiatan);
                    $keteranganList[] = $prefix . $clean($item->keterangan);
                }

                // Masukkan data ter-grouping dengan pemisah baris baru (\n)
                $template->setValue("no#{$i}", $i);
                $template->setValue("hari#{$i}", $hari);
                $template->setValue("tanggal#{$i}", $clean($tglFormatted));
                $template->setValue("jam_ke#{$i}", implode("\n", $jamList));
                $template->setValue("materi#{$i}", implode("\n", $materiList));
                $template->setValue("kegiatan#{$i}", implode("\n", $kegiatanList));
                $template->setValue("keterangan#{$i}", implode("\n", $keteranganList));

                $i++;
            }

            // 9. Buat file temp unik
            $tempFile = tempnam(sys_get_temp_dir(), 'rekap_jurnal_') . '.docx';
            $template->saveAs($tempFile);

            // 10. Bersihkan buffer output murni
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