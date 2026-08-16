<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Matikan pengecekan FK sementara agar MySQL tidak protes tipe data
        Schema::disableForeignKeyConstraints();

        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa']);
            $table->enum('tipe_absen', ['harian', 'mapel'])->default('harian');
            
            $table->string('mapel')->nullable();
            $table->string('jam_ke')->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });

        // Nyalakan kembali pengecekan FK
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('absensis');
        Schema::enableForeignKeyConstraints();
    }
};