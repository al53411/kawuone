<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'absensis';

    /**
     * Kolom yang diizinkan untuk dikelola via mass-assignment.
     */
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'guru_id',
        'tanggal',
        'status',
        'mapel',
        'keterangan',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke model Siswa (Setiap data absensi dimiliki oleh 1 siswa).
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke model Kelas (Setiap data absensi terikat dengan 1 kelas).
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke model User / Guru (Siapa guru yang mengisi absensi).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}