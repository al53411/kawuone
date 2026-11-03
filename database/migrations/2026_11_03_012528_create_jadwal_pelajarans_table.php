<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('kelas');       // Contoh: Kelas 4
            $table->string('wali_kelas');  // Nama wali kelas
            $table->string('hari');        // Senin, Selasa, Rabu, Kamis, Jumat
            $table->integer('jam_ke');     // Jam pelajaran ke- (1, 2, 3, dst.)
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
