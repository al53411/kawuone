<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Matikan Foreign Key Constraint
        Schema::disableForeignKeyConstraints();

        // 2. Jalankan seeder dengan urutan yang tepat
        $this->call([
            UserSeeder::class,
            KelasSeeder::class, // <-- Buat Kelas terlebih dahulu
            GuruSeeder::class,
            SiswaSeeder::class,
        ]);

        // 3. Aktifkan kembali Foreign Key Constraint
        Schema::enableForeignKeyConstraints();
    }
}