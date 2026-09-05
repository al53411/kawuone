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
        // 1. Handling Format Tanggal Lahir
        $tanggalLahir = null;
        if (!empty($row['tanggal_lahir'])) {
            if (is_numeric($row['tanggal_lahir'])) {
                $tanggalLahir = Carbon::instance(ExcelDate::excelToDateTimeObject($row['tanggal_lahir']))->format('Y-m-d');
            } else {
                $tanggalLahir = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
            }
        }

        $identifier = !empty($row['nip']) ? $row['nip'] : $row['nik'];

        // 2. Buat/Update User Account
        $user = User::updateOrCreate(
            ['email' => $identifier . '@sekolah.id'],
            [
                'name'       => $row['nama_lengkap'],
                'nip'        => $row['nip'] ?? null,
                'password'   => Hash::make($identifier),
                'role'       => 'guru',
                'sekolah_id' => $this->sekolahId,
            ]
        );

        // 3. Buat/Update Data Guru berdasarkan NIK atau NIP
        return Guru::updateOrCreate(
            ['nik' => $row['nik']], // Menggunakan NIK sebagai penanda unik
            [
                'user_id'             => $user->id,
                'sekolah_id'          => $this->sekolahId,
                'nip'                 => $row['nip'] ?? null,
                'nuptk'               => $row['nuptk'] ?? null,
                'nama_lengkap'        => $row['nama_lengkap'],
                'tempat_lahir'        => $row['tempat_lahir'],
                'tanggal_lahir'       => $tanggalLahir,
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
            ]
        );
    }
}