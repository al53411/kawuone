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
        Schema::create('kelas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');
        $table->string('nama_kelas');
        $table->string('wali_kelas')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};