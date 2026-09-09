<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'sekolah_id',
        'tahun',
        'semester',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public static function getAktif($sekolahId = null)
    {
        // BENAR: Gunakan boolean `true` untuk mengambil Tahun Ajaran AKTIF
        $query = static::where('is_aktif', true);

        if ($sekolahId) {
            $query->where('sekolah_id', $sekolahId);
        }

        return $query->first();
    }
}