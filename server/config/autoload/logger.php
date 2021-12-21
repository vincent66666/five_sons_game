<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
return [
    'default' => [
        'handler' => [
            'class'       => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
                'level'  => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => null,
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];
