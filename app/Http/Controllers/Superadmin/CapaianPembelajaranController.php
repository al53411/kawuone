<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CapaianPembelajaran;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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

        // Pastikan kunci Fase selalu urut (A -> B -> C -> D)
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
     * Memproses upload & ekstrak dokumen CP menggunakan Gemini AI Native Vision
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

        $parsedData = [];

        // 1. EKSTRAKSI VISUAL LANGSUNG (BASE64) AGAR TABEL TERBACA SEMPURNA
        if ($file) {
            $mimeType = $file->getClientMimeType();
            $fileData = base64_encode(file_get_contents($file->getPathname()));

            $parsedData = $this->parseDocumentNativeAI($fileData, $mimeType, $selectedFase);
        }

        // 2. FALLBACK JIKA MENGGUNAKAN TEKS MANUAL (TEXTAREA)
        if (empty($parsedData) && $request->filled('deskripsi')) {
            $parsedData = $this->parseTextNativeAI((string) $request->deskripsi, $selectedFase);
        }

        if (empty($parsedData)) {
            return redirect()->back()->with('error', 'Gagal memproses dokumen. Pastikan API Key valid dan format berkas jelas.');
        }

        // 3. SIMPAN HASIL PARSING KE DATABASE
        $savedCount = 0;
        foreach ($parsedData as $item) {
            if (!empty($item['elemen']) && !empty($item['deskripsi_cp'])) {

                $faseItem = strtoupper(trim($item['fase'] ?? ''));
                $faseFinal = ($selectedFase !== 'AUTO')
                    ? $selectedFase
                    : (in_array($faseItem, ['A', 'B', 'C', 'D', 'E', 'F']) ? $faseItem : 'A');

                // Validasi Lapis Kedua: Cegah SKI masuk Fase A/B secara tidak sengaja
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

        return redirect()->back()->with('success', "Berhasil diproses! {$savedCount} Elemen CP berhasil diekstrak tepat dari dalam tabel.");
    }

    /**
     * Mengirim file PDF/Word utuh ke Vision API Gemini agar struktur tabel tidak rusak
     */
    private function parseDocumentNativeAI(string $base64Data, string $mimeType, string $selectedFase): array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) return [];

        if ($selectedFase !== 'AUTO') {
            $fasePrompt = "PENTING: Seluruh dokumen ini adalah KHUSUS untuk FASE {$selectedFase}. Tetapkan atribut \"fase\": \"{$selectedFase}\" pada semua objek JSON.";
        } else {
            $fasePrompt = "Identifikasi secara visual header Fase/Kelas pada dokumen (Contoh: FASE A, FASE B). Kelompokkan CP berdasarkan fase yang tertulis.";
        }

        $prompt = "Anda adalah Sistem Ekstraksi Kurikulum Merdeka. Analisis dokumen PDF/Word terlampir secara visual.

INSTRUKSI KETAT (HANYA BACA TABEL CP):
1. {$fasePrompt}
2. BACA HANYA ISI DI DALAM TABEL. Abaikan sepenuhnya paragraf pendahuluan, paragraf \"Capaian Umum\", atau teks di luar garis tabel.
3. Ekstrak pasangan \"Elemen\" dan \"Capaian Pembelajaran (CP) Spesifik\" langsung dari sel tabel.
4. Salin deskripsi CP secara VERBATIM (Persis huruf per huruf sesuai di tabel dokumen). DILARANG MERANGKUM ATAU MENAMBAHKAN KATA.
5. Bersihkan nama sekolah, nama penyusun, dan kode Taksonomi Bloom (seperti C1, C2) jika ada.
6. Kembalikan HANYA format JSON array valid tanpa penjelasan tambahan apapun.

Format JSON:
[
  {
    \"fase\": \"C\",
    \"elemen\": \"Al-Qur'an Hadis\",
    \"deskripsi_cp\": \"Peserta didik mampu membaca, menghafal, dan menganalisis pesan pokok Q.S. al-Hujurat...\"
  }
]";

        try {
            // PERBAIKAN: Menambahkan withoutVerifying() untuk Bypass SSL Localhost Windows
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data'      => $base64Data
                                ]
                            ],
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.0, // Wajib 0.0 agar persis sesuai isi tabel asli
                ]
            ]);

            if ($response->status() === 429) {
                session()->flash('error', 'Batas kuota AI gratis (15x/menit) tersentuh. Tunggu 1 menit lalu coba lagi.');
                return [];
            }

            if ($response->successful()) {
                $responseData = $response->json();
                $aiContent = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

                $cleanJson = preg_replace('/```json\s*|\s*```/', '', $aiContent);
                $result = json_decode(trim($cleanJson), true);

                return is_array($result) ? $result : [];
            }
        } catch (\Exception $e) {
            // PERBAIKAN: Menampilkan pesan error asli jika gagal koneksi
            dd("Error API Vision: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Fallback parsing jika teks ditempel manual di textarea
     */
    private function parseTextNativeAI(string $rawText, string $selectedFase): array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) return [];

        $fasePrompt = ($selectedFase !== 'AUTO')
            ? "Set atribut \"fase\": \"{$selectedFase}\" untuk semua item."
            : "Tentukan fase (A-F) secara mandiri.";

        $prompt = "Ekstrak teks Capaian Pembelajaran berikut ke format JSON. {$fasePrompt} Abaikan paragraf Capaian Umum. Fokus hanya pada pasangan Elemen dan isi spesifik CP. Kembalikan array JSON murni. Teks: " . Str::limit($rawText, 15000, '');

        try {
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.0]
            ]);

            if ($response->successful()) {
                $aiContent = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $cleanJson = preg_replace('/```json\s*|\s*```/', '', $aiContent);
                return json_decode(trim($cleanJson), true) ?? [];
            }
        } catch (\Exception $e) {
            // PERBAIKAN: Menampilkan pesan error asli jika gagal koneksi
            dd("Error API Text: " . $e->getMessage());
        }

        return [];
    }
}
