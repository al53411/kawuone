<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'kelas';

    // Kolom yang dapat diisi massal (Mass Assignment)
    protected $fillable = [
        'sekolah_id',
        'guru_id',     // Foreign key ID Guru sebagai Wali Kelas
        'nama_kelas',
        'wali_kelas',  // Backup nama wali kelas (string)
    ];

    /**
     * Relasi ke Guru (Wali Kelas)
     */
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke Siswa (Satu kelas memiliki banyak siswa)
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Relasi Many-to-Many ke Guru (Guru Pengampu Mapel/Sesi)
     */
    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_kelas', 'kelas_id', 'guru_id');
    }

    /**
     * Relasi ke Sekolah (jika tabel menggunakan sekolah_id)
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }
}