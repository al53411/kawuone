<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Akun Superadmin Utama
            User::create([
                'name'     => 'Super Admin',
                'email'    => 'superadmin@admin.com',
                'password' => Hash::make('password123'),
                'role'     => 'superadmin',
            ]);

            // 2. Akun Admin Sekolah
            User::create([
                'name'     => 'Admin Sekolah',
                'email'    => 'admin@sekolah.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]);

            // 3. Akun Guru (Menggunakan NIP sebagai Username & Password)
            $nipGuru = '198501012010011001';

            $userGuru = User::create([
                'name'     => 'Guru Contoh, S.Pd.',
                'email'    => $nipGuru . '@sekolah.id', // Format email: 198501012010011001@sekolah.id
                'password' => Hash::make($nipGuru),       // Password default = NIP
                'role'     => 'guru',
            ]);

            // Opsional: Jika ada tabel `gurus`, buatkan juga data detail gurunya
            Guru::create([
                'user_id'             => $userGuru->id,
                'nip'                 => $nipGuru,
                'nik'                 => '3201234567890001',
                'nama_lengkap'        => 'Guru Contoh, S.Pd.',
                'tempat_lahir'        => 'Jakarta',
                'tanggal_lahir'       => '1985-01-01',
                'jenis_kelamin'       => 'L',
                'nama_ibu_kandung'    => 'Ibu Contoh',
                'status_kepegawaian'  => 'PNS',
                'pendidikan_terakhir' => 'S1 Pendidikan',
            ]);
        });
    }
}