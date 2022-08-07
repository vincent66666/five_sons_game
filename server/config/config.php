<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

$app_env   = env('APP_ENV');
$log_level = [
    LogLevel::ALERT,
    LogLevel::CRITICAL,
    //    LogLevel::DEBUG,
    LogLevel::EMERGENCY,
    LogLevel::ERROR,
    LogLevel::INFO,
    LogLevel::NOTICE,
    LogLevel::WARNING,
];
if ($app_env == 'local') {
    $log_level = [
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::DEBUG,
        LogLevel::EMERGENCY,
        LogLevel::ERROR,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
    ];
}

return [
    'app_name'                   => env('APP_NAME', 'skeleton'),
    'app_env'                    => env('APP_ENV', 'dev'),
    'scan_cacheable'             => env('SCAN_CACHEABLE', false),
    StdoutLoggerInterface::class => [
        'log_level' => $log_level,
    ],
];
