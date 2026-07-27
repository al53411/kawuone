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
        // Data Guru
        $gurus = [
            [
                'nama_guru' => 'Alif Sairoji, S.Pd.',
                'nip'       => '199508122020121001',
            ],
            [
                'nama_guru' => 'Budi Santoso, S.Pd.',
                'nip'       => '199203152019031002',
            ],
            [
                'nama_guru' => 'Guru SDN Kawu 1', // Ganti sesuai nama guru pemilik NIP ini
                'nip'       => '199209102023211006',
            ],
        ];

        foreach ($gurus as $data) {
            // 1. Simpan/Update ke tabel 'gurus'
            Guru::updateOrCreate(
                ['nip' => $data['nip']],
                ['nama_guru' => $data['nama_guru']]
            );

            // 2. Simpan/Update ke tabel 'users' (NIP sebagai Username & Password)
            User::updateOrCreate(
                ['nip' => $data['nip']],
                [
                    'name'     => $data['nama_guru'],
                    'role'     => 'guru',
                    'password' => Hash::make($data['nip']), // Password di-hash dari NIP
                ]
            );
        }
    }
}