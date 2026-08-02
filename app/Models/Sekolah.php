<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolahs';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'jenjang',
        'status',
        'alamat',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'email',
        'telepon',
        'nama_kepsek',
        'nip_kepsek',
    ];

    /**
     * Relasi ke Model User
     */
    public function users()
    {
        return $this->hasMany(User::class, 'sekolah_id');
    }

    /**
     * Relasi ke Model Guru
     */
    public function gurus()
    {
        return $this->hasMany(Guru::class, 'sekolah_id');
    }

    /**
     * Relasi ke Model Kelas
     */
    public function kelases()
    {
        return $this->hasMany(Kelas::class, 'sekolah_id');
    }

    /**
     * Relasi ke Model Siswa
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'sekolah_id');
    }

    /**
     * Relasi ke Model Jurnal
     * (Menghubungkan Sekolah langsung dengan tabel jurnals via sekolah_id)
        */
        public function jurnals()
    {
        // 'sekolah_id' ada di tabel users, 'user_id' ada di tabel jurnal_gurus
        return $this->hasManyThrough(
            JurnalGuru::class, 
            User::class, 
            'sekolah_id', // Foreign key di tabel users
            'user_id',    // Foreign key di tabel jurnal_gurus
            'id',         // Local key di tabel sekolahs
            'id'          // Local key di tabel users
        );
    }
}