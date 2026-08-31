<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Matikan pengecekan Foreign Key sementara
        Schema::disableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            GuruSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
        ]);

        // Aktifkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();
    }
}