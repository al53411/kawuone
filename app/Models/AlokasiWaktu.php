<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlokasiWaktu extends Model
{
    use HasFactory;

    protected $table = 'alokasi_waktus';

    protected $fillable = [
        'kelas',
        'mapel_id',
        'jumlah_jam',
    ];

    /**
     * Relasi ke model Mapel (Setiap alokasi waktu milik satu mata pelajaran)
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}