<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole; // 1. Import middleware kamu

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 2. Percayai proxy Vercel agar HTTPS terbaca sempurna (Menghilangkan error Form is not secure)
        $middleware->trustProxies(at: '*');

        // 3. Daftarkan alias 'role' di sini
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();