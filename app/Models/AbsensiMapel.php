<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiMapel extends Model
{
    protected $fillable = ['siswa_id', 'kelas_id', 'mapel', 'tanggal', 'pertemuan_ke', 'status'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}