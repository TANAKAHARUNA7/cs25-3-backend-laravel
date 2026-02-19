<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    
    // 例外（エラー）設定
    ->withExceptions(function (Exceptions $exceptions) {

        // 未認証エラー（AuthenticationException）が発生したときの処理
        $exceptions->render(function(
            // 未ログイン時に投げられる例外
            Illuminate\Auth\AuthenticationException $e,
            // 今のHTTPリクエスト
            Illuminate\Http\Request $request
        ){
            // APIリクエスト（JSONを期待している場合）のみ
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => [
                        'code'    => 'UNAUTHORIZED',
                        'message' => 'ログインが必要です.'
                    ]
                ], 401);
            }
        });
    })->create();
