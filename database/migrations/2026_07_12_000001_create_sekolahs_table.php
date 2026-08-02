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
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('npsn')->unique();
            $table->string('nama_sekolah');
            $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'SMK', 'TK', 'PAUD'])->default('SD');
            $table->enum('status', ['Negeri', 'Swasta'])->default('Negeri');
            $table->text('alamat')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('nama_kepsek')->nullable();
            $table->string('nip_kepsek')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};