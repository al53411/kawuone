<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'sekolah_id',
        'guru_id',
        'nama_kelas',
        'wali_kelas',
    ];

    // Relasi tunggal ke Guru (Wali Kelas)
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Alias relasi waliKelas
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Relasi ke Guru jika dipanggil dalam bentuk jamak (gurus)
    public function gurus()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Relasi ke Siswa (Satu kelas memiliki banyak siswa)
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    // Relasi ke Sekolah
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }
}