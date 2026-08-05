<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Cek apakah kolomnya belum ada, baru tambahkan
            if (!Schema::hasColumn('kelas', 'sekolah_id')) {
                $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'sekolah_id')) {
                $table->dropColumn('sekolah_id');
            }
        });
    }
};
