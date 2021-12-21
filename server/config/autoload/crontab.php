<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
$app_env   = env('APP_ENV');
return [
    // 是否开启定时任务
    'enable'  => $app_env != 'local',
    // 通过配置文件定义的定时任务
    'crontab' => [
        // Callback类型定时任务（默认）
    ],
];
