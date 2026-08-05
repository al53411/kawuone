<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah tabel kelas BELUM ada sebelum dibuat
        if (!Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');
                $table->string('nama_kelas');
                $table->string('wali_kelas')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};