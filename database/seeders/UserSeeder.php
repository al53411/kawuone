<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Superadmin Utama
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@admin.com',
            'password' => Hash::make('password123'),
            'role'     => 'superadmin',
        ]);

        // Akun Admin Sekolah
        User::create([
            'name'     => 'Admin Sekolah',
            'email'    => 'admin@sekolah.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Akun Guru
        User::create([
            'name'     => 'Guru Contoh',
            'email'    => 'guru@sekolah.com',
            'password' => Hash::make('password123'),
            'role'     => 'guru',
        ]);
    }
}