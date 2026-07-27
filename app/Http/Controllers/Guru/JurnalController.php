<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalGuru;
use App\Models\Kelas;
use App\Models\ProfilSekolah;
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
    public function cetakPdf(Request $request)
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

        // 2. Path File Template Word dan Output
        $templatePath = storage_path('app/templates/template_rekap.docx');
        $outputPath = storage_path('app/public/Rekap_Jurnal_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $user->name) . '.docx');

        // Cek keberadaan file template Word
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template Word tidak ditemukan di: storage/app/templates/template_rekap.docx');
        }

        try {
            // 3. Inisialisasi PHPWord Template Processor
            $template = new TemplateProcessor($templatePath);

            // 4. Set Variable Header / Profil Guru
            Carbon::setLocale('id');
            $template->setValue('nama_guru', $user->name ?? '-');
            $template->setValue('nip', $user->nip ?? '-');
            $template->setValue('bulan', Carbon::now()->translatedFormat('F Y'));

            // 5. Duplikasi Baris Tabel Rekap
            $totalData = count($jurnals);
            $template->cloneRow('no', $totalData);

            foreach ($jurnals as $index => $item) {
                $i = $index + 1;
                $tglFormatted = Carbon::parse($item->tanggal)->translatedFormat('d M Y');

                $template->setValue("no#{$i}", $i);
                $template->setValue("hari#{$i}", $item->hari ?? '-');
                $template->setValue("tanggal#{$i}", $tglFormatted);
                $template->setValue("jam_ke#{$i}", $item->jam_ke ?? '-');
                $template->setValue("kelas#{$i}", $item->kelas->nama_kelas ?? '-');
                $template->setValue("mapel#{$i}", $item->mapel ?? '-');
                $template->setValue("materi#{$i}", $item->materi ?? '-');
                $template->setValue("kegiatan#{$i}", $item->kegiatan ?? '-');
                $template->setValue("keterangan#{$i}", $item->keterangan ?? '-');
                $template->setValue("status#{$i}", $item->status_validasi ?? 'Pending');
            }

            // 6. Simpan File DOCX
            $template->saveAs($outputPath);

            // 7. Download File DOCX dan hapus setelah terkirim
            return response()->download($outputPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses template Word: Pastikan tag ${no} ada di dalam baris tabel Word. Error: ' . $e->getMessage());
        }
    }
}