<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
return [
    'http' => [
        \App\Middleware\CorsMiddleware::class,
    ],
    'socket-io' => [
        \App\Middleware\WebSocketAuthMiddleware::class,
    ],
];
