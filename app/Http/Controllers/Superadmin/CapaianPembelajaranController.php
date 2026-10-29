<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CapaianPembelajaran;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

class CapaianPembelajaranController extends Controller
{
    /**
     * Menampilkan daftar CP dengan Filter per Fase & Layout Merged Rowspan
     */
    public function index(Request $request)
    {
        $filterFase = strtoupper($request->get('fase', 'ALL'));

        $query = CapaianPembelajaran::with('mapel')
            ->orderBy('fase', 'asc')
            ->latest();

        if ($filterFase !== 'ALL' && in_array($filterFase, ['A', 'B', 'C', 'D', 'E', 'F'])) {
            $query->where('fase', $filterFase);
        }

        $allCps = $query->get();

        // Urutan hirarki Elemen Agama Islam / Kurikulum Merdeka
        $elemenPriority = [
            "Al-Qur'an Hadis"           => 1,
            "Al-Qur'an dan Hadis"       => 1,
            "Akidah"                    => 2,
            "Akhidak"                   => 2,
            "Akhlak"                    => 3,
            "Fikih"                     => 4,
            "Sejarah Peradaban Islam"   => 5,
            "SKI"                       => 5,
        ];

        // Filter Aturan Khusus: SKI TIDAK BOLEH ADA DI FASE A & B
        $filteredCps = $allCps->reject(function ($item) {
            $fase = strtoupper($item->fase);
            $elemen = strtolower($item->elemen);

            $isSki = str_contains($elemen, 'sejarah') || str_contains($elemen, 'ski');
            return $isSki && in_array($fase, ['A', 'B']);
        });

        // Grouping 2 Tingkat: [Fase][Mapel_ID] -> Collection of CP
        $cpsNested = $filteredCps->groupBy(['fase', 'mapel_id']);

        $faseOrder = ['A', 'B', 'C', 'D', 'E', 'F'];
        $cpsByFase = collect();

        foreach ($faseOrder as $faseKey) {
            if ($cpsNested->has($faseKey)) {
                $mapelGroups = $cpsNested->get($faseKey)->map(function ($items) use ($elemenPriority) {
                    return $items->sortBy(function ($item) use ($elemenPriority) {
                        return $elemenPriority[$item->elemen] ?? 99;
                    })->values();
                });
                $cpsByFase->put($faseKey, $mapelGroups);
            }
        }

        foreach ($cpsNested as $key => $mapelGroups) {
            if (!$cpsByFase->has($key)) {
                $cpsByFase->put($key, $mapelGroups);
            }
        }

        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();

        return view('superadmin.capaian_pembelajaran.index', compact('cpsByFase', 'mapels', 'filterFase'));
    }

    /**
     * Memproses upload & ekstrak dokumen CP secara lokal (Bebas Error SSL/API)
     */
    public function importPdf(Request $request)
    {
        $request->validate([
            'mapel_id'    => 'required|exists:mapels,id',
            'fase'        => 'nullable|string',
            'file_berkas' => 'nullable|mimes:pdf,docx,doc|max:10240',
            'file_pdf'    => 'nullable|mimes:pdf,docx,doc|max:10240',
            'deskripsi'   => 'nullable|string',
        ], [
            'mapel_id.required' => 'Mata pelajaran wajib dipilih!',
        ]);

        $selectedFase = strtoupper(trim($request->input('fase', 'AUTO')));
        $file = $request->file('file_berkas') ?? $request->file('file_pdf');
        $rawText = '';

        // 1. EKSTRAKSI TEKS MENTAH MENGGUNAKAN PARSER LOKAL (PDF / WORD)
        if ($file) {
            $extension = strtolower($file->getClientOriginalExtension());
            try {
                if (in_array($extension, ['docx', 'doc'])) {
                    if (class_exists('\PhpOffice\PhpWord\IOFactory')) {
                        $phpWord = IOFactory::load($file->getPathname());
                        $textBuffer = [];
                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                if (method_exists($element, 'getText')) {
                                    $textBuffer[] = $element->getText();
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                                    foreach ($element->getRows() as $row) {
                                        foreach ($row->getCells() as $cell) {
                                            foreach ($cell->getElements() as $cellElement) {
                                                if (method_exists($cellElement, 'getText')) {
                                                    $textBuffer[] = $cellElement->getText();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $rawText = implode("\n", $textBuffer);
                    }
                } elseif ($extension === 'pdf') {
                    if (class_exists('\Smalot\PdfParser\Parser')) {
                        $parser = new Parser();
                        $pdf = $parser->parseFile($file->getPathname());
                        $rawText = $pdf->getText();
                    }
                }
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        // Fallback ke textarea jika kosong
        if (empty(trim($rawText)) && $request->filled('deskripsi')) {
            $rawText = (string) $request->deskripsi;
        }

        if (empty(trim($rawText))) {
            return redirect()->back()->with('error', 'Teks dari berkas tidak terbaca. Silakan tempel teks CP secara manual.');
        }

        // 2. PARSING LOKAL BERDASARKAN KATA KUNCI ELEMEN
        $parsedData = $this->localRegexParser($rawText, $selectedFase);

        if (empty($parsedData)) {
            return redirect()->back()->with('error', 'Gagal mengekstrak data CP dari berkas.');
        }

        // 3. SIMPAN KE DATABASE
        $savedCount = 0;
        foreach ($parsedData as $item) {
            if (!empty($item['elemen']) && !empty($item['deskripsi_cp'])) {

                $faseFinal = ($selectedFase !== 'AUTO') ? $selectedFase : ($item['fase'] ?? 'C');

                // Validasi Aturan SKI di Fase A & B
                $isSki = str_contains(strtolower($item['elemen']), 'sejarah') || str_contains(strtolower($item['elemen']), 'ski');
                if ($isSki && in_array($faseFinal, ['A', 'B'])) {
                    continue;
                }

                CapaianPembelajaran::create([
                    'mapel_id'     => $request->mapel_id,
                    'fase'         => $faseFinal,
                    'elemen'       => Str::limit($item['elemen'], 100, ''),
                    'deskripsi_cp' => trim($item['deskripsi_cp']),
                    'sekolah_id'   => null,
                ]);
                $savedCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil memproses dan menyimpan {$savedCount} Elemen CP ke database secara lokal!");
    }

    /**
     * Parser Lokal Berbasis Keyword Elemen PAI & Umum
     */
    private function localRegexParser(string $text, string $selectedFase): array
    {
        $cleanText = preg_replace('/\s+/', ' ', trim($text));

        $elemenList = [
            "Al-Qur'an Hadis",
            "Akidah",
            "Akhlak",
            "Fikih",
            "Sejarah Peradaban Islam",
            "SKI"
        ];

        $faseTarget = ($selectedFase !== 'AUTO') ? $selectedFase : 'C';
        $results = [];

        $pattern = '/(' . implode('|', array_map('preg_quote', $elemenList)) . ')/i';
        $parts = preg_split($pattern, $cleanText, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if (is_array($parts) && count($parts) >= 2) {
            for ($i = 0; $i < count($parts); $i++) {
                $candidate = trim($parts[$i]);
                foreach ($elemenList as $el) {
                    if (strcasecmp($candidate, $el) === 0) {
                        $isiCp = trim($parts[$i + 1] ?? '');
                        $isiCp = preg_replace('/^[\s\:\-\.]+/i', '', $isiCp);

                        if (!empty($isiCp)) {
                            $results[] = [
                                'fase'         => $faseTarget,
                                'elemen'       => $el,
                                'deskripsi_cp' => $isiCp
                            ];
                        }
                        break;
                    }
                }
            }
        }

        // Jika tidak terdeteksi lewat keyword, masukkan sebagai teks utama
        if (empty($results)) {
            $results[] = [
                'fase'         => $faseTarget,
                'elemen'       => 'Al-Qur\'an Hadis',
                'deskripsi_cp' => $cleanText
            ];
        }

        return $results;
    }
}
