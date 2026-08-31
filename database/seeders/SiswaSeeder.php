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
                'nisn'          => '0123456789',
                'nama_siswa'    => 'Randi Perkasa',
                'kelas_id'      => $kelas->id, // ✅ Ambil ID kelas secara dinamis
                'jenis_kelamin' => 'L',
                'alamat'        => 'Kec. Padas, Ngawi',
            ]);
        }
    }
}