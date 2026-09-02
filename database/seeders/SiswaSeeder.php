<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kelas pertama yang ada di database
        $kelas = Kelas::first();

        // Pastikan kelas ditemukan sebelum membuat siswa
        if ($kelas) {
            Siswa::create([
                
            ]);
        }
    }
}