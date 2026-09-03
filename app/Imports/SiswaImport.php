<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $sekolahId;

    public function __construct($sekolahId = null)
    {
        $this->sekolahId = $sekolahId;
    }

    public function model(array $row)
    {
        // 1. Abaikan baris jika NISN kosong
        if (empty($row['nisn'])) {
            return null;
        }

        // 2. Validasi Kelas: Cari ID Kelas berdasarkan ID atau Nama Kelas di DB
        $kelasId = null;
        $inputKelas = $row['kelas_id'] ?? $row['kelas'] ?? null;

        if (!empty($inputKelas)) {
            // Cari berdasarkan ID atau nama kelas
            $kelas = Kelas::where('id', $inputKelas)
                ->orWhere('nama_kelas', $inputKelas)
                ->orWhere('nama', $inputKelas)
                ->first();

            if ($kelas) {
                $kelasId = $kelas->id;
            }
        }

        // 3. Ambil Nama Siswa (bisa dari header 'nama_siswa' atau 'nama')
        $namaSiswa = $row['nama_siswa'] ?? $row['nama'] ?? 'Tanpa Nama';

        // 4. Simpan ke Database
        return new Siswa([
            'sekolah_id'    => $this->sekolahId ?? $row['sekolah_id'] ?? null,
            'nisn'          => (string) $row['nisn'],
            'nama_siswa'    => $namaSiswa, // Menggunakan nama_siswa sesuai kolom DB
            'jenis_kelamin' => !empty($row['jenis_kelamin']) ? strtoupper($row['jenis_kelamin']) : 'L',
            'alamat'        => $row['alamat'] ?? null,
            'kelas_id'      => $kelasId, // Jika tidak ketemu akan terisi NULL (aman)
        ]);
    }
}