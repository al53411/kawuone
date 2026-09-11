<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianPembelajaran extends Model
{
    use HasFactory;

    protected $table = 'capaian_pembelajarans';

    protected $fillable = [
        'mapel_id',
        'fase',
        'elemen',
        'deskripsi_cp',
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}