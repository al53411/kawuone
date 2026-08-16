<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');

            $table->string('nisn')->nullable()->unique();
            $table->string('nama_lengkap');
            
            // Kolom pendukung yang dibutuhkan oleh controller & form
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->text('alamat')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};