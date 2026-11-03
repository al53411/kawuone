<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CapaianPembelajaran;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
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

        $elemenPriority = [
            "Al-Qur'an Hadis"           => 1,
            "Al-Qur'an dan Hadis"       => 1,
            "Akidah"                    => 2,
            "Akhlak"                    => 3,
            "Fikih"                     => 4,
            "Sejarah Peradaban Islam"   => 5,
            "SKI"                       => 5,
        ];

        $filteredCps = $allCps->reject(function ($item) {
            $fase = strtoupper($item->fase);
            $elemen = strtolower($item->elemen);
            $isSki = str_contains($elemen, 'sejarah') || str_contains($elemen, 'ski');
            return $isSki && in_array($fase, ['A', 'B']);
        });

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

    public function previewImport(Request $request)
    {
        $request->validate([
            'mapel_id'    => 'required|exists:mapels,id',
            'fase'        => 'nullable|string',
            'file_berkas' => 'nullable|mimes:pdf,docx,doc,jpg,jpeg,png|max:15240',
            'deskripsi'   => 'nullable|string',
        ]);

        $selectedFase = strtoupper(trim($request->input('fase', 'AUTO')));
        $file = $request->file('file_berkas');
        $parsedData = [];
        $rawText = '';

        if ($file) {
            $extension = strtolower($file->getClientOriginalExtension());

            // 1. WAJIB: Gunakan Gemini AI untuk file PDF DAN Gambar agar teks tabel utuh dan tidak terpotong
            if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'])) {
                $parsedData = $this->parseWithGeminiVision($file, $selectedFase);
            }
            // 2. Jika file berupa Word (.docx)
            elseif (in_array($extension, ['docx', 'doc'])) {
                try {
                    if (class_exists('\PhpOffice\PhpWord\IOFactory')) {
                        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getPathname());
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
                } catch (\Exception $e) {
                }
            }
        }

        // 3. Fallback ke textarea jika file tidak diunggah atau teks manual diisi
        if (empty($parsedData) && empty(trim($rawText)) && $request->filled('deskripsi')) {
            $rawText = (string) $request->deskripsi;
        }

        // 4. Jika menggunakan Word atau teks manual, jalankan parser lokal
        if (empty($parsedData)) {
            if (empty(trim($rawText))) {
                return redirect()->route('superadmin.capaian-pembelajaran.index')
                    ->with('error', 'Silakan unggah berkas PDF/Word atau tempel teks CP terlebih dahulu!');
            }
            $parsedData = $this->localRegexParser($rawText, $selectedFase);
        }

        if (empty($parsedData)) {
            return redirect()->route('superadmin.capaian-pembelajaran.index')
                ->with('error', 'Gagal mengekstrak data CP. Pastikan dokumen terbaca dengan jelas.');
        }

        $mapel = Mapel::findOrFail($request->mapel_id);

        return view('superadmin.capaian_pembelajaran.preview', compact('parsedData', 'mapel', 'selectedFase', 'rawText'));
    }

    /**
     * Menyimpan data secara batch setelah disetujui di halaman preview (Anti Duplikat)
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'mapel_id'     => 'required|exists:mapels,id',
            'fase'         => 'required|array',
            'elemen'       => 'required|array',
            'deskripsi_cp' => 'required|array',
        ]);

        $mapelId = $request->mapel_id;
        $fases = $request->fase;
        $elemens = $request->elemen;
        $deskripsis = $request->deskripsi_cp;

        $savedCount = 0;
        for ($i = 0; $i < count($elemens); $i++) {
            if (!empty($elemens[$i]) && !empty($deskripsis[$i])) {
                $faseFinal = $fases[$i] ?? 'C';

                $isSki = str_contains(strtolower($elemens[$i]), 'sejarah') || str_contains(strtolower($elemens[$i]), 'ski');
                if ($isSki && in_array($faseFinal, ['A', 'B'])) {
                    continue;
                }

                CapaianPembelajaran::updateOrCreate(
                    [
                        'mapel_id' => $mapelId,
                        'fase'     => $faseFinal,
                        'elemen'   => Str::limit($elemens[$i], 100, ''),
                    ],
                    [
                        'deskripsi_cp' => trim($deskripsis[$i]),
                        'sekolah_id'   => null,
                    ]
                );
                $savedCount++;
            }
        }

        return redirect()->route('superadmin.capaian-pembelajaran.index')
            ->with('success', "Berhasil menyimpan secara permanen {$savedCount} Elemen CP ke database tanpa duplikasi!");
    }

    public function update(Request $request, CapaianPembelajaran $capaianPembelajaran)
    {
        $validated = $request->validate([
            'mapel_id'     => 'required|exists:mapels,id',
            'fase'         => 'required|string|max:20',
            'elemen'       => 'required|string|max:150',
            'deskripsi_cp' => 'required|string',
        ]);

        $capaianPembelajaran->update($validated);

        return redirect()->route('superadmin.capaian-pembelajaran.index')
            ->with('success', 'Capaian Pembelajaran berhasil diperbarui!');
    }

    public function destroy(CapaianPembelajaran $capaianPembelajaran)
    {
        $capaianPembelajaran->delete();

        return redirect()->route('superadmin.capaian-pembelajaran.index')
            ->with('success', 'Capaian Pembelajaran berhasil dihapus!');
    }

    /**
     * Integrasi Gemini AI untuk membaca dokumen PDF / Gambar secara utuh & akurat
     */
    private function parseWithGeminiVision($file, string $selectedFase): array
    {
        try {
            $mimeType = $file->getMimeType();
            $fileData = base64_encode(file_get_contents($file->getPathname()));

            $apiKey = env('GEMINI_API_KEY');
            if (empty($apiKey)) {
                return [];
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

            $faseInstruction = ($selectedFase === 'AUTO')
                ? "Ekstrak seluruh data dari dokumen ini untuk SEMUA FASE yang ada (Fase A, Fase B, Fase C, dll.). Tentukan huruf fase (A, B, atau C) secara akurat untuk setiap baris elemen."
                : "Fokuskan ekstraksi khusus untuk FASE {$selectedFase}. Pastikan kolom 'fase' bernilai '{$selectedFase}'.";

            $prompt = "Anda adalah asisten AI kurikulum pendidikan Indonesia. 
            Analisis dokumen Kurikulum Merdeka yang dilampirkan (PDF atau Gambar) secara menyeluruh. 
            Ketentuan ekstraksi:
            1. {$faseInstruction}
            2. Ekstrak elemen-elemen resmi (contoh: Al-Qur'an Hadis, Akidah, Akhlak, Fikih, Sejarah Peradaban Islam / SPI).
            3. Ambil deskripsi atau cakupan pembelajaran secara utuh dan lengkap dari tabel tanpa ada yang terpotong.
            4. Hindari duplikasi data.
            
            Keluarkan hasil akhir HANYA dalam format JSON array murni tanpa backtick markdown, dengan struktur object:
            [
              {
                \"fase\": \"A\",
                \"elemen\": \"Nama Elemen\",
                \"deskripsi_cp\": \"Isi deskripsi utuh lengkap...\"
              }
            ]";

            $response = Http::post($url, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt],
                            [
                                "inline_data" => [
                                    "mime_type" => $mimeType,
                                    "data" => $fileData
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $resultText = $response->json('candidates.0.content.parts.0.text');
                $cleanedJson = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', $resultText);
                $parsedData = json_decode(trim($cleanedJson), true);

                if (is_array($parsedData)) {
                    return $parsedData;
                }
            }
        } catch (\Exception $e) {
        }

        return [];
    }

    /**
     * Parser Lokal Pintar (Membaca Teks Secara Offline & Mengurai Blok FASE Dokumen Resmi)
     */
    private function localRegexParser(string $text, string $selectedFase): array
    {
        $cleanText = preg_replace('/[ \t\r\n]+/u', ' ', trim($text));

        $elemenList = [
            "Al-Qur'an dan Hadis",
            "Al-Qur'an Hadis",
            "Akidah",
            "Akhlak",
            "Fikih",
            "Sejarah Peradaban Islam",
            "SKI"
        ];

        $results = [];

        // 1. Coba Identifikasi Blok Berdasarkan "FASE A", "FASE B", dst.
        preg_match_all('/FASE\s+([A-F])\s*\(([^)]+)\)(.*?)(?=(FASE\s+[A-F]|$))/ui', $cleanText, $faseBlocks, PREG_SET_ORDER);

        if (!empty($faseBlocks)) {
            foreach ($faseBlocks as $block) {
                $faseHuruf = strtoupper(trim($block[1]));
                $faseContent = $block[3];

                if ($selectedFase !== 'AUTO' && $faseHuruf !== $selectedFase) {
                    continue;
                }

                $sortedElemen = $elemenList;
                usort($sortedElemen, function ($a, $b) {
                    return strlen($b) - strlen($a);
                });

                foreach ($sortedElemen as $el) {
                    $pos = stripos($faseContent, $el);
                    if ($pos !== false) {
                        $startPos = $pos + strlen($el);

                        $nextPos = PHP_INT_MAX;
                        foreach ($sortedElemen as $subEl) {
                            $p = stripos($faseContent, $subEl, $startPos);
                            if ($p !== false && $p < $nextPos) {
                                $nextPos = $p;
                            }
                        }

                        $subText = ($nextPos !== PHP_INT_MAX)
                            ? substr($faseContent, $startPos, $nextPos - $startPos)
                            : substr($faseContent, $startPos);

                        $isiCp = preg_replace('/^[\s\:\-\.]+/u', '', trim($subText));

                        if (!empty($isiCp) && strlen($isiCp) > 10) {
                            $normalizedElemen = $el;
                            if (strtolower($el) === "al-qur'an hadis") {
                                $normalizedElemen = "Al-Qur'an dan Hadis";
                            }

                            $exists = collect($results)->contains(function ($item) use ($faseHuruf, $normalizedElemen) {
                                return $item['fase'] === $faseHuruf && $item['elemen'] === $normalizedElemen;
                            });

                            if (!$exists) {
                                $results[] = [
                                    'fase'         => $faseHuruf,
                                    'elemen'       => $normalizedElemen,
                                    'deskripsi_cp' => $isiCp
                                ];
                            }
                        }
                    }
                }
            }
        }

        // 2. Jika tidak ada blok FASE (Misal paste teks berantakan), gunakan pencarian urut linear
        if (empty($results)) {
            $faseTarget = ($selectedFase !== 'AUTO') ? $selectedFase : 'C';
            if ($selectedFase === 'AUTO') {
                if (preg_match('/fase\s*([a-f])/i', $cleanText, $match)) {
                    $faseTarget = strtoupper($match[1]);
                }
            }

            $sortedElemen = $elemenList;
            usort($sortedElemen, function ($a, $b) {
                return strlen($b) - strlen($a);
            });

            $foundElements = [];
            foreach ($sortedElemen as $el) {
                $pos = stripos($cleanText, $el);
                if ($pos !== false) {
                    $exists = collect($foundElements)->contains(function ($item) use ($pos) {
                        return abs($item['pos'] - $pos) < 5;
                    });

                    if (!$exists) {
                        $foundElements[] = ['elemen' => $el, 'pos' => $pos];
                    }
                }
            }

            usort($foundElements, function ($a, $b) {
                return $a['pos'] - $b['pos'];
            });

            for ($i = 0; $i < count($foundElements); $i++) {
                $current = $foundElements[$i];
                $currentElemenName = $current['elemen'];
                $startPos = $current['pos'] + strlen($currentElemenName);

                if (isset($foundElements[$i + 1])) {
                    $endPos = $foundElements[$i + 1]['pos'];
                    $subText = substr($cleanText, $startPos, $endPos - $startPos);
                } else {
                    $subText = substr($cleanText, $startPos);
                }

                $isiCp = preg_replace('/^[\s\:\-\.]+/u', '', trim($subText));

                if (!empty($isiCp)) {
                    $normalizedElemen = $currentElemenName;
                    if (strtolower($currentElemenName) === "al-qur'an hadis") {
                        $normalizedElemen = "Al-Qur'an dan Hadis";
                    }

                    $results[] = [
                        'fase'         => $faseTarget,
                        'elemen'       => $normalizedElemen,
                        'deskripsi_cp' => $isiCp
                    ];
                }
            }
        }

        return $results;
    }
}
