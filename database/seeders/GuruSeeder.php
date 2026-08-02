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
                'nama_lengkap'        => 'Alif Sairoji, S.Pd.',
                'tempat_lahir'        => 'Ngawi',
                'tanggal_lahir'       => '1995-08-12',
                'jenis_kelamin'       => 'L',
                'nama_ibu_kandung'    => 'Siti Aminah',
                'nip'                 => '199508122020121001',
                'status_kepegawaian'  => 'PPPK',
                'golongan'            => 'IX',
                'jabatan'             => 'Guru Kelas',
                'pendidikan_terakhir' => 'S-1',
            ],
            [
                'nik'                 => '3515011503920002',
                'nama_lengkap'        => 'Budi Santoso, S.Pd.',
                'tempat_lahir'        => 'Surabaya',
                'tanggal_lahir'       => '1992-03-15',
                'jenis_kelamin'       => 'L',
                'nama_ibu_kandung'    => 'Sri Rahayu',
                'nip'                 => '199203152019031002',
                'status_kepegawaian'  => 'PNS',
                'golongan'            => 'III/a',
                'jabatan'             => 'Guru Penjasorkes',
                'pendidikan_terakhir' => 'S-1',
            ],
            [
                'nik'                 => '3515011009920003',
                'nama_lengkap'        => 'Siti Nurhaliza, S.Pd.',
                'tempat_lahir'        => 'Madiun',
                'tanggal_lahir'       => '1992-09-10',
                'jenis_kelamin'       => 'P',
                'nama_ibu_kandung'    => 'Sumarni',
                'nip'                 => '199209102023211006',
                'status_kepegawaian'  => 'GTT',
                'golongan'            => null,
                'jabatan'             => 'Guru Bahasa Inggris',
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