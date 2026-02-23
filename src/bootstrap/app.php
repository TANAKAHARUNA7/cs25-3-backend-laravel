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

    // 403　権限エラー
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    
    // その他例外設定
    ->withExceptions(function (Exceptions $exceptions) {

        // 401 未認証エラー
        $exceptions->render(function(
            // 未ログイン時に投げられる例外
            \Illuminate\Auth\AuthenticationException $e,
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


        // 404　指定されたリソースが存在しない
        $exceptions->render(function(
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            $request
        ){
            if($request->expectsJson()) {
                return response()->json([
                'success' => false,
                'error'   => [
                    'code'    => 'RESOURCE_NOT_FOUND',
                    'message' => '該当するリソースが見つかりません' 
                    ]
                ], 404);
            }
            
        });

    })->create();
