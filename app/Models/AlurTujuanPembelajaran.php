<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlurTujuanPembelajaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Tujuan Pembelajaran (TP)
    public function tujuanPembelajaran()
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tujuan_pembelajaran_id');
    }

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}