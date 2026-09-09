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

    /**
     * Helper untuk mengambil Tahun Ajaran aktif berdasarkan sekolah
     */
    public static function getAktif($sekolahId = null)
    {
        $query = self::where('is_aktif', true);

        if ($sekolahId) {
            $query->where('sekolah_id', $sekolahId);
        }

        return $query->first();
    }
}