<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            
            // Relasi Opsional (ke tabel users & sekolahs jika ada)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->onDelete('cascade');

            // -------------------------------------------------------------
            // 1. IDENTITAS PRIBADI (Validasi Dukcapil)
            // -------------------------------------------------------------
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap'); // Tanpa gelar sesuai akta
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_ibu_kandung');

            // -------------------------------------------------------------
            // 2. STATUS KEPEGAWAIAN (Database BKN)
            // -------------------------------------------------------------
            $table->string('nip', 18)->unique()->nullable(); // Nullable jika ada guru Non-ASN
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'GTT', 'GTY'])->default('PPPK');
            $table->string('golongan')->nullable(); // Contoh: III/a, IX, dsb.
            $table->string('jabatan')->nullable();  // Contoh: Ahli Pertama, Ahli Muda
            
            // -------------------------------------------------------------
            // 3. TUGAS UTAMA & MATA PELAJARAN
            // -------------------------------------------------------------
            $table->string('jenis_guru')->nullable();    // Tugas Mengajar / Guru Kelas / Guru Mapel
            $table->string('mata_pelajaran')->nullable(); // Nama Mata Pelajaran

            $table->date('tmt_sk')->nullable();     // Terhitung Mulai Tanggal SK
            $table->integer('mkg_tahun')->default(0); // Masa Kerja Golongan (Tahun)
            $table->integer('mkg_bulan')->default(0); // Masa Kerja Golongan (Bulan)

            // -------------------------------------------------------------
            // 4. KUALIFIKASI & SERTIFIKASI (Dapodik)
            // -------------------------------------------------------------
            $table->string('pendidikan_terakhir')->default('S-1'); // S-1 / D-4
            $table->string('nuptk', 16)->unique()->nullable();
            $table->string('no_serdik')->nullable(); // Nomor Sertifikat Pendidik
            $table->string('nrg')->nullable();       // Nomor Register Guru

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};