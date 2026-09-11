<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->enum('fase', ['A', 'B', 'C', 'D', 'E', 'F']); // A=Kelas 1-2, B=3-4, C=5-6 (SD)
            $table->string('elemen'); // Contoh: Pemahaman IPAS, Menulis, Geometri
            $table->text('deskripsi_cp'); // Teks Narasi Capaian Pembelajaran
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_pembelajarans');
    }
};