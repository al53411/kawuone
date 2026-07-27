<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::create([
            'nisn' => '0123456789',
            'nama_siswa' => 'Randi Perkasa',
            'kelas_id' => 1, // Mengarah ke Kelas 1
            'jenis_kelamin' => 'L',
            'alamat' => 'Kec. Padas, Ngawi'
        ]);

        Siswa::create([
            'nisn' => '0123456790',
            'nama_siswa' => 'Amalia Putri',
            'kelas_id' => 2, // Mengarah ke Kelas 2
            'jenis_kelamin' => 'P',
            'alamat' => 'Desa Kawu, Ngawi'
        ]);
    }
}