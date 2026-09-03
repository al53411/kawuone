<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class GuruImport implements ToModel, WithHeadingRow
{
    protected $sekolahId;

    public function __construct($sekolahId = null)
    {
        $this->sekolahId = $sekolahId;
    }

    public function model(array $row)
    {
        // 1. Handling Format Tanggal Lahir (Mengatasi Angka Serial 33857)
        $tanggalLahir = null;
        if (!empty($row['tanggal_lahir'])) {
            if (is_numeric($row['tanggal_lahir'])) {
                // Konversi angka serial Excel (misal: 33857) menjadi YYYY-MM-DD
                $tanggalLahir = Carbon::instance(ExcelDate::excelToDateTimeObject($row['tanggal_lahir']))->format('Y-m-d');
            } else {
                // Jika sudah berupa teks format tanggal
                $tanggalLahir = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
            }
        }

        // 2. Tentukan Identifier untuk Email dan Password Default
        $identifier = !empty($row['nip']) ? $row['nip'] : $row['nik'];

        // 3. Buat Akun User Login
        $user = User::create([
            'name'       => $row['nama_lengkap'],
            'nip'        => $row['nip'] ?? null,
            'email'      => $identifier . '@sekolah.id',
            'password'   => Hash::make($identifier),
            'role'       => 'guru',
            'sekolah_id' => $this->sekolahId,
        ]);

        // 4. Buat Record Data Guru
        return new Guru([
            'user_id'             => $user->id,
            'sekolah_id'          => $this->sekolahId,
            'nik'                 => $row['nik'],
            'nip'                 => $row['nip'] ?? null,
            'nuptk'               => $row['nuptk'] ?? null,
            'nama_lengkap'        => $row['nama_lengkap'],
            'tempat_lahir'        => $row['tempat_lahir'],
            'tanggal_lahir'       => $tanggalLahir, // <--- Hasil konversi aman dari error SQL
            'jenis_kelamin'       => $row['jenis_kelamin'],
            'nama_ibu_kandung'    => $row['nama_ibu_kandung'],
            'status_kepegawaian'  => $row['status_kepegawaian'] ?? 'GTT',
            'golongan'            => $row['golongan'] ?? null,
            'jabatan'             => $row['jabatan'] ?? null,
            'jenis_guru'          => $row['jenis_guru'] ?? null,
            'mata_pelajaran'      => $row['mata_pelajaran'] ?? null,
            'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? null,
            'no_serdik'           => $row['no_serdik'] ?? null,
            'nrg'                 => $row['nrg'] ?? null,
        ]);
    }
}