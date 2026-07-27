<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat 1 Akun Super Admin Utama
        User::create([
            // 'sekolah_id' => null, // Super Admin tidak terikat ke satu sekolah khusus
            'name'     => 'Super Administrator',
            'email'    => 'superadmin@gmail.com',
            'role'     => 'admin', // Role admin
            'password' => Hash::make('password123'), // Password admin
        ]);

        // 2. Memanggil Seeder Guru (otomatis buat data guru + akun login NIP)
        $this->call([
            GuruSeeder::class,
        ]);
    }
}