<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            // Menambahkan kolom kategori dengan pilihan: kbm, rutin, ekstra
            $table->enum('kategori', ['kbm', 'rutin', 'ekstra'])
                ->default('kbm')
                ->after('nama_mapel');
        });
    }

    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
