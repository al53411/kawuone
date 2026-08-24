<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Route Test untuk memastikan API berjalan
Route::get('/v1/login', function () {
    return response()->json(['status' => 'success', 'message' => 'API Vercel Berhasil Aktif!']);
});

// Route Login POST untuk Android
Route::post('/v1/login', [AuthController::class, 'login']);