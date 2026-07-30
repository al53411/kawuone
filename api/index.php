<?php

// 1. Arahkan direktori penyimpanan sementara ke /tmp Vercel
$storagePath = '/tmp/storage';
$bootstrapCachePath = '/tmp/bootstrap/cache';

// Buat direktori jika belum ada
if (!file_exists($storagePath)) {
    mkdir($storagePath . '/framework/views', 0755, true);
    mkdir($storagePath . '/framework/cache/data', 0755, true);
    mkdir($storagePath . '/framework/sessions', 0755, true);
    mkdir($storagePath . '/logs', 0755, true);
}

if (!file_exists($bootstrapCachePath)) {
    mkdir($bootstrapCachePath, 0755, true);
}

// 2. Set environment variable untuk bootstrap cache path
putenv("APP_STORAGE={$storagePath}");
$_ENV['APP_STORAGE'] = $storagePath;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bind path storage & bootstrap cache ke /tmp
$app->useStoragePath($storagePath);
$app->useBootstrapPath('/tmp/bootstrap');

// 3. Jalankan HTTP Kernel Laravel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);