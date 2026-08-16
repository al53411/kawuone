<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // UBAH DARI INI:
    Siswa::create([
        'nisn' => '0123456789',
        'nama_siswa' => 'Randi Perkasa', // <-- Hapus nama_siswa
        'kelas_id' => 1,
        'jenis_kelamin' => 'L',
        'alamat' => 'Kec. Padas, Ngawi',
    ]);

    // MENJADI INI:
    Siswa::create([
        'nisn' => '0123456789',
        'nama_lengkap' => 'Randi Perkasa', // <-- Ubah jadi nama_lengkap
        'kelas_id' => 1,
        'jenis_kelamin' => 'L',
        'alamat' => 'Kec. Padas, Ngawi',
    ]);
    }
}