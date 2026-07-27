<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalGuru extends Model
{
    use HasFactory;

    // Nama tabel disesuaikan dengan migration
    protected $table = 'jurnal_gurus'; 

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'hari',
        'tanggal',
        'jam_ke',
        'mapel',
        'materi',
        'kegiatan',
        'keterangan',
        'status_validasi',
        'catatan_kepsek',
        'tanggal_validasi'
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}