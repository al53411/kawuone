<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alur_tujuan_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('tujuan_pembelajaran_id')->constrained('tujuan_pembelajarans')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('kode_atp', 50);
            $table->enum('semester', ['1', '2']);
            $table->integer('urutan')->default(1);
            $table->integer('alokasi_jp')->default(2);
            $table->text('rincian_materi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alur_tujuan_pembelajarans');
    }
};