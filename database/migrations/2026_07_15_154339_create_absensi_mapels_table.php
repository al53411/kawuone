<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_mapels', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi Sekolah & Guru / Penginput Data (Penting untuk Multi-Sekolah)
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->onDelete('set null');

            // 2. Relasi Siswa & Kelas
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');

            // 3. Relasi Mapel
            $table->foreignId('mapel_id')->nullable()->constrained('mapels')->onDelete('cascade');

            $table->date('tanggal');
            
            // Pertemuan ke- berfungsi sebagai penanda "Minggu ke-" (1, 2, 3, dst.)
            $table->unsignedSmallInteger('pertemuan_ke'); 

            // 4. Status Absensi Menggunakan Enum
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa'])->default('Hadir');

            $table->text('keterangan')->nullable(); // Catatan tambahan (opsional)
            $table->timestamps();

            // 5. Indexing untuk mempercepat query saat filter/cetak absensi
            $table->index(['kelas_id', 'tanggal']);

            // 6. [TAMBAHAN BARU] Mencegah Duplikat Absen
            // Memastikan 1 siswa hanya bisa diabsen 1 kali per mapel pada pertemuan/minggu yang sama
            $table->unique(
                ['siswa_id', 'mapel_id', 'pertemuan_ke'], 
                'cegah_duplikat_absen_siswa'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_mapels');
    }
};