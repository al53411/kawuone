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
        Schema::create('alokasi_waktus', function (Blueprint $table) {
            $table->id();
            $table->string('kelas'); // Contoh: Kelas 1, Kelas 2, dst.

            // Berelasi ke tabel 'mapels' yang sudah ada
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');

            $table->integer('jumlah_jam')->default(0); // Alokasi jam per minggu
            $table->timestamps();

            // Mencegah duplikasi alokasi mapel yang sama pada kelas yang sama
            $table->unique(['kelas', 'mapel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alokasi_waktus');
    }
};
