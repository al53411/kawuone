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

Route::get('/', function () {
    return view('welcome');
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

    // Validasi Jurnal oleh Kepala Sekolah / Admin (Ubah nama method agar tidak banguan dengan update data Kepsek)
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