<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database (mencegah pencarian otomatis ke 'kelases').
     */
    protected $table = 'kelas';

    /**
     * Kolom yang dapat diisi massal (Mass Assignment).
     */
    protected $fillable = [
        'sekolah_id',
        'guru_id',     // Foreign key ID Guru sebagai Wali Kelas
        'nama_kelas',
        'wali_kelas',  // Backup nama wali kelas (string)
    ];

    /**
     * Relasi ke Guru (Wali Kelas)
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke Siswa (Satu kelas memiliki banyak siswa)
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Relasi Many-to-Many ke Guru (Guru Pengampu Mapel/Sesi)
     */
    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_kelas', 'kelas_id', 'guru_id');
    }

    /**
     * Relasi ke Sekolah (jika tabel menggunakan sekolah_id)
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    /**
     * Relasi ke Absensi (Satu kelas memiliki banyak data absensi)
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'kelas_id');
    }
}