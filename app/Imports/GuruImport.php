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
        // Skip baris jika NIK kosong
        if (empty($row['nik'])) {
            return null;
        }

        // Helper Konversi Tanggal Excel / String ke Y-m-d
        $parseDate = function ($value) {
            if (empty($value)) return null;
            try {
                if (is_numeric($value)) {
                    return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
                }
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        $tanggalLahir = $parseDate($row['tanggal_lahir'] ?? null);
        $tmtSk        = $parseDate($row['tmt_sk'] ?? null);

        $identifier = !empty($row['nip']) ? $row['nip'] : $row['nik'];
        $email      = $identifier . '@sekolah.id';

        // 1. Cari atau Buat User Baru (Mencegah Reset Password jika User Sudah Ada)
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name'       => $row['nama_lengkap'],
                'nip'        => $row['nip'] ?? null,
                'email'      => $email,
                'password'   => Hash::make($identifier),
                'role'       => 'guru',
                'sekolah_id' => $this->sekolahId,
            ]);
        } else {
            // Update nama & nip tanpa mengubah password
            $user->update([
                'name'       => $row['nama_lengkap'],
                'nip'        => $row['nip'] ?? null,
                'sekolah_id' => $this->sekolahId,
            ]);
        }

        // 2. Buat atau Update Data Guru berdasarkan NIK
        return Guru::updateOrCreate(
            ['nik' => (string) $row['nik']], // Cast ke string mencegah integer overflow
            [
                'user_id'             => $user->id,
                'sekolah_id'          => $this->sekolahId,
                'nip'                 => $row['nip'] ?? null,
                'nuptk'               => $row['nuptk'] ?? null,
                'nama_lengkap'        => $row['nama_lengkap'],
                'tempat_lahir'        => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'       => $tanggalLahir,
                'jenis_kelamin'       => strtoupper($row['jenis_kelamin'] ?? 'L'),
                'nama_ibu_kandung'    => $row['nama_ibu_kandung'] ?? null,
                'status_kepegawaian'  => $row['status_kepegawaian'] ?? 'GTT',
                'golongan'            => $row['golongan'] ?? null,
                'jabatan'             => $row['jabatan'] ?? null,
                'jenis_guru'          => $row['jenis_guru'] ?? null,
                'mata_pelajaran'      => $row['mata_pelajaran'] ?? null,
                'tmt_sk'              => $tmtSk,
                'mkg_tahun'           => isset($row['mkg_tahun']) ? (int) $row['mkg_tahun'] : 0,
                'mkg_bulan'           => isset($row['mkg_bulan']) ? (int) $row['mkg_bulan'] : 0,
                'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? 'S-1',
                'no_serdik'           => $row['no_serdik'] ?? null,
                'nrg'                 => $row['nrg'] ?? null,
            ]
        );
    }
}