<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // 1. Hapus foreign key yang mengarah ke 'kelases'
            $table->dropForeign(['kelas_id']);

            // 2. Buat foreign key baru yang mengarah ke tabel 'kelas'
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->foreign('kelas_id')->references('id')->on('kelases')->onDelete('cascade');
        });
    }
};