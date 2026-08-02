<?php

use App\Http\Controllers\ProfileController;

// Import Controller Superadmin / Admin Pusat
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\SekolahController as SuperadminSekolahController;
use App\Http\Controllers\Superadmin\UserController;

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

// Redirect Halaman Utama langsung ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// REDIRECT DASHBOARD BERDASARKAN ROLE
// ==========================================
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    } 
    
    if (in_array($user->role, ['admin', 'admin_sekolah', 'kepsek'])) {
        return redirect()->route('admin.dashboard');
    } 
    
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

    // CRUD Sekolah oleh Superadmin
    Route::resource('sekolah', SuperadminSekolahController::class);

    // CRUD Kepala Sekolah / Management Account oleh Superadmin
    Route::resource('kepsek', UserController::class);
});


// ==========================================
// GROUP ROUTE KHUSUS ADMIN SEKOLAH / KEPSEK
// ==========================================
Route::middleware(['auth', 'role:admin,admin_sekolah,kepsek,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Validasi Jurnal oleh Kepala Sekolah / Admin
    Route::get('/kepala-sekolah/jurnal', [AdminKepalaSekolahController::class, 'indexValidasiJurnal'])->name('kepala-sekolah.jurnal.index');
    Route::put('/kepala-sekolah/jurnal/{id}', [AdminKepalaSekolahController::class, 'updateStatusJurnal'])->name('kepala-sekolah.jurnal.update');

    // Reset Password Guru
    Route::post('/guru/{guru}/reset-password', [AdminGuruController::class, 'resetPassword'])->name('guru.reset-password');

    // Route Resource Fitur Admin Sekolah
    Route::resource('absensi', AdminAbsensiController::class);
    Route::resource('guru', AdminGuruController::class);
    Route::resource('kelas', AdminKelasController::class);
    Route::resource('sekolah', AdminSekolahController::class);
    Route::resource('siswa', AdminSiswaController::class);
    Route::resource('jurnal', AdminJurnalController::class);

    // Resource Kepala Sekolah (Tingkat Sekolah)
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
    
    // Route Cetak PDF
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
        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed'  => true,
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