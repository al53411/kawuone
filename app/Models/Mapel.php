<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapels';

    protected $fillable = [
        'sekolah_id',
        'kode_mapel',
        'nama_mapel',
    ];

    /**
     * Relasi ke Sekolah (jika ada)
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
}