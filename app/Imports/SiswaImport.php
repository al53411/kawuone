<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $sekolahId;

    public function __construct($sekolahId = null)
    {
        $this->sekolahId = $sekolahId;
    }

    public function model(array $row)
    {
        // 1. Abaikan baris kosong
        if (empty($row['nisn'])) {
            return null;
        }

        // 2. Tangkap nilai kelas dari berbagai kemungkinan nama header Excel
        $rawKelas = $row['kelas_id'] 
                 ?? $row['kelas'] 
                 ?? $row['id_kelas'] 
                 ?? $row['rombel'] 
                 ?? $row['nama_kelas'] 
                 ?? null;

        $kelasId = null;

        if (!empty($rawKelas)) {
            $rawKelas = trim((string) $rawKelas);

            // A. Jika di Excel langsung diisi angka ID Kelas (misal: 2)
            if (is_numeric($rawKelas)) {
                $cekKelas = Kelas::find($rawKelas);
                if ($cekKelas) {
                    $kelasId = $cekKelas->id;
                }
            }

            // B. Jika ID tidak ditemukan, cari berdasarkan nama kelas / tingkat
            if (!$kelasId) {
                $sekolahIdTarget = $this->sekolahId ?? $row['sekolah_id'] ?? null;

                $kelas = Kelas::when($sekolahIdTarget, function ($q) use ($sekolahIdTarget) {
                        return $q->where('sekolah_id', $sekolahIdTarget);
                    })
                    ->where(function ($q) use ($rawKelas) {
                        $q->where('nama_kelas', 'LIKE', "%{$rawKelas}%")
                          ->orWhere('nama', 'LIKE', "%{$rawKelas}%")
                          ->orWhere('tingkat', $rawKelas);
                    })
                    ->first();

                if ($kelas) {
                    $kelasId = $kelas->id;
                }
            }
        }

        // 3. Tangkap nama siswa
        $namaSiswa = $row['nama_siswa'] ?? $row['nama'] ?? $row['nama_lengkap'] ?? 'Tanpa Nama';

        // 4. Simpan ke Database
        return Siswa::updateOrCreate(
            ['nisn' => (string) $row['nisn']],
            [
                'sekolah_id'    => $this->sekolahId ?? $row['sekolah_id'] ?? 1,
                'nama_siswa'    => $namaSiswa,
                'jenis_kelamin' => !empty($row['jenis_kelamin']) ? strtoupper(trim($row['jenis_kelamin'])) : 'L',
                'alamat'        => $row['alamat'] ?? null,
                'kelas_id'      => $kelasId,
            ]
        );
    }
}