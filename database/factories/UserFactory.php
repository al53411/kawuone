<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nisn'          => fake()->unique()->numerify('00########'),
            'nama_siswa'    => fake()->name(), // <-- Ini kunci agar nama_siswa tidak NULL
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'kelas_id'      => Kelas::first()?->id ?? 1,
            'alamat'        => fake()->address(),
        ];
    }
}