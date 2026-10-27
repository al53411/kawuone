<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiMapel extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'absensi_mapels';

    /**
     * Kolom yang diizinkan untuk dikelola via mass-assignment.
     */
    protected $fillable = [
        'sekolah_id',
        'guru_id',
        'siswa_id',
        'kelas_id',
        'mapel_id', // Ubah menjadi 'mapel' jika di database Anda menggunakan string, bukan foreign key
        'tanggal',
        'pertemuan_ke',
        'status',
        'keterangan',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke model Sekolah.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    /**
     * Relasi ke model Guru (Siapa guru mapel yang mengabsen).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id'); 
        // Catatan: Jika guru_id di tabel Anda mengarah ke tabel users, ganti Guru::class menjadi User::class
    }

    /**
     * Relasi ke model Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke model Kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke model Mapel.
     */
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}