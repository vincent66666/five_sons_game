<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
return [
    'handler' => [
        'http' => [
            App\Exception\Handler\AuthExceptionHandler::class,
            App\Exception\Handler\ValidationExceptionHandler::class,
            App\Exception\Handler\ErrorResponseExceptionHandler::class,
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
