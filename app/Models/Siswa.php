<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'sekolah_id',
        'user_id',
        'nisn',
        'nama_siswa', // ✅ WAJIB nama_siswa (sesuai migration & seeder)
        'kelas_id',
        'jenis_kelamin',
        'alamat'
    ];

    /**
     * Relasi ke model Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}