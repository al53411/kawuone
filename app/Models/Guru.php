<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'sekolah_id',
        
        // 1. Identitas Pribadi
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_ibu_kandung',

        // 2. Status Kepegawaian
        'nip',
        'status_kepegawaian',
        'golongan',
        'jabatan',
        'tmt_sk',
        'mkg_tahun',
        'mkg_bulan',

        // 3. Kualifikasi & Sertifikasi
        'pendidikan_terakhir',
        'nuptk',
        'no_serdik',
        'nrg',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Sekolah
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * Relasi Many-to-Many ke Kelas (Guru Pengampu Mapel/Sesi via Pivot `guru_kelas`)
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas', 'guru_id', 'kelas_id');
    }

    /**
     * Relasi One-to-Many ke Kelas (Jika Guru menjadi Wali Kelas)
     */
    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }
}