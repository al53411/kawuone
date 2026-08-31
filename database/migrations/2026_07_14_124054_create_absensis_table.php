<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke siswa (wajib)
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            
            // Relasi ke kelas (nullable agar tidak error jika ID kelas tidak ditemukan)
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            
            // Relasi ke users/guru (nullable agar admin/sistem bisa mengabsen)
            $table->foreignId('guru_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Alpa']);
            $table->enum('tipe_absen', ['harian', 'mapel'])->default('harian');
            
            $table->string('mapel')->nullable();
            $table->string('jam_ke')->nullable();
            $table->text('catatan')->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('absensis');
        Schema::enableForeignKeyConstraints();
    }
};