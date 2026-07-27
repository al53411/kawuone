<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
            Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete(); // atau $table->unsignedBigInteger('sekolah_id')->nullable();
            $table->string('name');
            $table->string('nip')->unique()->nullable(); // Tambahkan kolom NIP
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('role')->default('guru'); // admin / guru
            $table->rememberToken();
            $table->timestamps();
          });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};