<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_formatifs', function (Blueprint $table) {
            $table->id();
            
            // Relasi Utama
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->foreignId('tp_id')->constrained('tujuan_pembelajarans')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            
            // Data Penilaian (Perbaikan: decimal()->unsigned())
            $table->decimal('nilai', 5, 2)->unsigned()->comment('Skala Nilai 0 - 100');
            $table->text('catatan')->nullable()->comment('Deskripsi/Catatan Capaian TP Siswa');

            $table->timestamps();

            // Indeks Komposit untuk Kecepatan Query & Mencegah Duplikasi
            $table->unique(['siswa_id', 'tp_id'], 'unique_siswa_tp_formatif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_formatifs');
    }
};