<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JurnalApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Seluruh route di file ini otomatis mendapatkan prefix '/api'
| Contoh endpoint: https://kawuone.vercel.app/api/v1/login
*/

// Endpoint Public (Tanpa Token Auth)
Route::post('/v1/login', [AuthController::class, 'login']);

// Endpoint Simpan Jurnal
// Catatan: Jika ingin diproteksi token login (Sanctum), gunakan middleware auth:sanctum
Route::post('/v1/jurnal', [JurnalApiController::class, 'store']);