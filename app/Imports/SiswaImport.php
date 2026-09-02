<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $sekolahId;

    // Terima ID Sekolah (opsional jika sistem multi-sekolah)
    public function __construct($sekolahId = null)
    {
        $this->sekolahId = $sekolahId;
    }

    public function model(array $row)
    {
        // Abaikan jika NISN kosong
        if (empty($row['nisn'])) {
            return null;
        }

        return new Siswa([
            'sekolah_id' => $this->sekolahId ?? $row['sekolah_id'] ?? null,
            'nisn'       => $row['nisn'],
            'nis'        => $row['nis'] ?? null,
            'nama'       => $row['nama'],
            'jenis_kelamin' => $row['jenis_kelamin'] ?? 'L', // L / P
            'kelas_id'   => $row['kelas_id'] ?? null,
        ]);
    }
}