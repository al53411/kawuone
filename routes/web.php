<?php

use App\Http\Controllers\ProfileController;

// Import Controller Superadmin / Admin Pusat
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;

// Import Controller Admin Sekolah
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\CetakAbsensiMapelController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\KelasController as AdminKelasController;
use App\Http\Controllers\Admin\KepalaSekolahController as AdminKepalaSekolahController;
use App\Http\Controllers\Admin\SekolahController as AdminSekolahController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\JurnalController as AdminJurnalController;

// Import Controller Guru
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\GuruSiswaController;
use App\Http\Controllers\Guru\JurnalController as GuruJurnalController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Redirect Halaman Utama langsung ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// REDIRECT DASHBOARD BERDASARKAN ROLE
// ==========================================
Route::get('/dashboard', function () {
    $user = Auth::user();

    // 1. Superadmin
    if ($user->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    } 
    
    // 2. Admin Sekolah & Kepsek
    if (in_array($user->role, ['admin', 'admin_sekolah', 'kepsek'])) {
        return redirect()->route('admin.dashboard');
    } 
    
    // 3. Guru
    if ($user->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// GROUP ROUTE KHUSUS SUPERADMIN
// ==========================================
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');

    // Route Tambah Kepsek oleh Superadmin
    Route::get('/kepsek/create', [AdminKepalaSekolahController::class, 'create'])->name('kepsek.create');
    Route::post('/kepsek', [AdminKepalaSekolahController::class, 'store'])->name('kepsek.store');

    // Route Edit, Update, & Hapus Sekolah
    Route::get('/sekolah/{id}/edit', [AdminSekolahController::class, 'edit'])->name('sekolah.edit');
    Route::put('/sekolah/{id}', [AdminSekolahController::class, 'update'])->name('sekolah.update');
    Route::delete('/sekolah/{id}', [AdminSekolahController::class, 'destroy'])->name('sekolah.destroy');
});


// ==========================================
// GROUP ROUTE KHUSUS ADMIN SEKOLAH / KEPSEK
// ==========================================
Route::middleware(['auth', 'role:admin,admin_sekolah,kepsek,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin Sekolah
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Validasi Jurnal oleh Kepala Sekolah / Admin
    Route::get('/kepala-sekolah/jurnal', [AdminKepalaSekolahController::class, 'indexValidasiJurnal'])->name('kepala-sekolah.jurnal.index');
    Route::put('/kepala-sekolah/jurnal/{id}', [AdminKepalaSekolahController::class, 'updateStatusJurnal'])->name('kepala-sekolah.jurnal.update');

    // Route Resource Fitur Admin Sekolah
    Route::resource('absensi', AdminAbsensiController::class);
    Route::resource('guru', AdminGuruController::class);
    Route::resource('kelas', AdminKelasController::class);
    Route::resource('sekolah', AdminSekolahController::class);
    Route::resource('siswa', AdminSiswaController::class);
    Route::resource('jurnal', AdminJurnalController::class);

    // Resource Kepala Sekolah
    Route::resource('kepala-sekolah', AdminKepalaSekolahController::class);

    // Cetak Absensi Mapel
    Route::get('/cetak-absensi-mapel', [CetakAbsensiMapelController::class, 'index'])->name('cetak-absensi-mapel.index');
});


// ==========================================
// GROUP ROUTE KHUSUS GURU
// ==========================================
Route::middleware(['auth', 'role:guru,superadmin'])->prefix('guru')->name('guru.')->group(function () {
    
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    Route::resource('siswa', GuruSiswaController::class);
    
    // Route Cetak PDF (Harus sebelum Route Resource Jurnal)
    Route::get('/jurnal/cetak-pdf', [GuruJurnalController::class, 'cetakPdf'])->name('jurnal.cetak');
    Route::resource('jurnal', GuruJurnalController::class);
});


// ==========================================
// PROFILE MANAGEMENT
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// ==========================================
// ROUTE MIGRATION SEMENTARA FOR VERCEL + SUPABASE
// ==========================================
Route::get('/run-migrate', function () {
    try {
        // 1. Reset Schema Postgres Supabase secara menyeluruh agar bersih
        DB::statement('DROP SCHEMA public CASCADE;');
        DB::statement('CREATE SCHEMA public;');

        // 2. Jalankan migration dari nol
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        // 3. Jalankan seeder
        Artisan::call('db:seed', [
            '--force' => true,
        ]);

        return '
            <div style="font-family: sans-serif; padding: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534;">
                <h2 style="margin-top:0;">✅ Migration & Seeding Berhasil!</h2>
                <pre style="background: #ffffff; padding: 15px; border-radius: 5px; border: 1px solid #e2e8f0; overflow-x: auto;">' . Artisan::output() . '</pre>
            </div>
        ';
    } catch (\Exception $e) {
        return '
            <div style="font-family: sans-serif; padding: 20px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b;">
                <h2 style="margin-top:0;">❌ Migration Gagal!</h2>
                <pre style="background: #ffffff; padding: 15px; border-radius: 5px; border: 1px solid #e2e8f0; overflow-x: auto;">' . $e->getMessage() . '</pre>
            </div>
        ';
    }
});