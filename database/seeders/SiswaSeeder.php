<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::first();

        // Ambil ID kelas pertama, jika tidak ada berikan default 1
        $kelasId = $kelas ? $kelas->id : 1;

        Siswa::create([
            'nisn'          => '0012345678',
            'nama_siswa'    => 'Ahmad Dahlan', // <-- Pastikan key ini ada dan tidak NULL
            'jenis_kelamin' => 'L',
            'kelas_id'      => $kelasId,
            'alamat'        => 'Jl. Raya Kawu No. 1',
        ]);
    }
}