<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Tambahkan baris di bawah ini untuk mengizinkan input data
    protected $fillable = [
        'nisn',
        'nama_siswa',
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