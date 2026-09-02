<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Data Guru Lengkap (Validasi Dukcapil, BKN, & Dapodik)
        $gurus = [
            [
                'nik'                 => '3515011208950001',
                'nama_lengkap'        => 'ALIF SAIROJI, S.Pd.',
                'tempat_lahir'        => 'Ngawi',
                'tanggal_lahir'       => '1992-09-10',
                'jenis_kelamin'       => 'L',
                'nama_ibu_kandung'    => 'Siti Aminah',
                'nip'                 => '199209102023211006',
                'status_kepegawaian'  => 'PPPK',
                'golongan'            => 'IX',
                'jabatan'             => 'Guru Kelas',
                'pendidikan_terakhir' => 'S-1',
            ],
        ];

        foreach ($gurus as $data) {
            // 1. Simpan / Update ke tabel 'gurus'
            Guru::updateOrCreate(
                ['nik' => $data['nik']], // Gunakan NIK sebagai unique identifier utama
                [
                    'nama_lengkap'        => $data['nama_lengkap'],
                    'tempat_lahir'        => $data['tempat_lahir'],
                    'tanggal_lahir'       => $data['tanggal_lahir'],
                    'jenis_kelamin'       => $data['jenis_kelamin'],
                    'nama_ibu_kandung'    => $data['nama_ibu_kandung'],
                    'nip'                 => $data['nip'],
                    'status_kepegawaian'  => $data['status_kepegawaian'],
                    'golongan'            => $data['golongan'],
                    'jabatan'             => $data['jabatan'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                ]
            );

            // 2. Simpan / Update ke tabel 'users' untuk Login Guru
            User::updateOrCreate(
                ['email' => $data['nip'] . '@sekolah.id'], // atau sesuaikan jika login pakai NIP / Email
                [
                    'name'     => $data['nama_lengkap'],
                    'role'     => 'guru',
                    'password' => Hash::make($data['nip']), // Password menggunakan NIP
                ]
            );
        }
    }
}