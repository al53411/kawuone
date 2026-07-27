<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            
            // Kolom Isian Utama
            $table->string('hari');                        // Contoh: Senin, Selasa
            $table->date('tanggal');                       // Tanggal Mengajar
            $table->string('jam_ke');                      // Contoh: Jam Ke 1-2 (07.00 - 08.10)
            $table->string('mapel');                       // Mata Pelajaran
            $table->text('materi');                        // Materi / TP Pembelajaran
            $table->text('kegiatan');                      // Kegiatan Pembelajaran
            $table->string('keterangan')->nullable();      // Keterangan (misal: Selesai, Dilanjutkan minggu depan)

            // Status & Validasi Kepala Sekolah
            $table->enum('status_validasi', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->text('catatan_kepsek')->nullable();
            $table->timestamp('tanggal_validasi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_gurus');
    }
};