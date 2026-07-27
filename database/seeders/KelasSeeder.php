<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menambahkan data kelas tiruan langsung ke database
        Kelas::create(['nama_kelas' => 'Kelas 1', 'wali_kelas' => 'Budi, S.Pd']);
        Kelas::create(['nama_kelas' => 'Kelas 2', 'wali_kelas' => 'Siti, S.Pd']);
        Kelas::create(['nama_kelas' => 'Kelas 3', 'wali_kelas' => 'Ahmad, S.Pd']);
        Kelas::create(['nama_kelas' => 'Kelas 4', 'wali_kelas' => 'Dewi, S.Pd']);
        Kelas::create(['nama_kelas' => 'Kelas 5', 'wali_kelas' => 'Eko, S.Pd']);
        Kelas::create(['nama_kelas' => 'Kelas 6', 'wali_kelas' => 'Rini, S.Pd']);
    }
}