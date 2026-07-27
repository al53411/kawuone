<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengunci nama tabel di database kamu
    protected $table = 'gurus'; 

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'jabatan',
        'golongan'
    ];
}