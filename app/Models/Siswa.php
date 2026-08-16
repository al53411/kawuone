<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Mengizinkan input data massal sesuai dengan nama kolom database
    protected $fillable = [
        'sekolah_id',
        'user_id',
        'nisn',
        'nama_lengkap', // <--- DIBETULKAN DARI nama_siswa KE nama_lengkap
        'kelas_id',
        'jenis_kelamin',
        'alamat'
    ];

    /**
     * Relasi ke model Kelas (Siswa milik suatu Kelas)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}