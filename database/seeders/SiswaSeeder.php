<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Cari kelas pertama yang ada di DB
        $kelas = Kelas::first();

        if ($kelas) {
            Siswa::updateOrCreate(
                ['nisn' => '0123456789'],
                [
                    'nama_siswa'    => 'Randi Perkasa',
                    'jenis_kelamin' => 'L',
                    'kelas_id'      => $kelas->id,
                    'alamat'        => 'Jl. Raya Kawu No. 1',
                ]
            );
        }
    }
}