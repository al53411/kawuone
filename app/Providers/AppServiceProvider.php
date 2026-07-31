<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL; // <-- 1. TAMBAHKAN IMPORT INI
use App\Models\Sekolah;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. PAKSA HTTPS DI PRODUCTION / VERCEL
        // Ini mencegah error "Form is not secure" saat submit data
        if (config('app.env') === 'production' || app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Cek dulu apakah tabel 'sekolahs' sudah dibuat di database agar tidak error saat migration
        if (Schema::hasTable('sekolahs')) {
            // Ambil data sekolah ID 1, jika kosong gunakan nama default
            $profilSekolah = Sekolah::first() ?? new Sekolah(['nama_sekolah' => 'SDN KAWU 1']);
        } else {
            $profilSekolah = new Sekolah(['nama_sekolah' => 'SDN KAWU 1']);
        }

        // Membagikan variabel $profilSekolah ke seluruh file blade (.blade.php)
        View::share('profilSekolah', $profilSekolah);
    }
}