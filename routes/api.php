<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Route Login POST untuk Android
Route::post('/v1/login', [AuthController::class, 'login']);