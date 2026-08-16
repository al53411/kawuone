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
            'nama_siswa' => 'Randi Perkasa', // ✅ GUNAKAN nama_siswa
            'kelas_id' => 1,
            'jenis_kelamin' => 'L',
            'alamat' => 'Kec. Padas, Ngawi',
        ]);
    }
}