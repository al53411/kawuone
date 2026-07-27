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
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable();
            $table->date('tanggal');
            $table->string('mapel');
            $table->text('materi');
            $table->text('kegiatan')->nullable();
            $table->enum('status_validasi', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->text('catatan_kepsek')->nullable();
            $table->timestamp('tanggal_validasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};
