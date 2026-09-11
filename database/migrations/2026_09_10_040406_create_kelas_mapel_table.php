<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            
            // Opsional: Jika ingin menentukan guru pengampu spesifik per kelas
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->onDelete('set null'); 
            
            $table->timestamps();

            // Mencegah duplikasi pasangan kelas dan mapel yang sama
            $table->unique(['kelas_id', 'mapel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_mapel');
    }
};