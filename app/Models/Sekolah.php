<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolahs'; // Menegaskan nama tabel (opsional tapi aman)

    protected $fillable = [
        'nama_sekolah', 
        'npsn', 
        'nama_kepala_sekolah', 
        'alamat_sekolah'
    ];

    /**
     * Relasi One-to-Many ke model User (Guru, Kepsek, Tendik)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'sekolah_id');
    }

    /**
     * Relasi khusus untuk mengambil User yang ber-role Kepsek (Opsional, mempermudah query)
     */
    public function kepsek()
    {
        return $this->hasOne(User::class, 'sekolah_id')->where('role', 'kepsek');
    }
}