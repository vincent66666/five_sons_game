<?php

declare(strict_types=1);

/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\Server\Event;
use Hyperf\Server\Server;
use Swoole\Constant;

return [
    'mode'      => SWOOLE_PROCESS,
    'servers'   => [
        [
            'name'      => 'http',
            'type'      => Server::SERVER_HTTP,
            'host'      => '0.0.0.0',
            'port'      => 9509,
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_REQUEST => [Hyperf\HttpServer\Server::class, 'onRequest'],
            ],
        ],
        [
            'name'      => 'socket-io',
            'type'      => Server::SERVER_WEBSOCKET,
            'host'      => '0.0.0.0',
            'port'      => 9702,
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_HAND_SHAKE => [Hyperf\WebSocketServer\Server::class, 'onHandShake'],
                Event::ON_MESSAGE    => [Hyperf\WebSocketServer\Server::class, 'onMessage'],
                Event::ON_CLOSE      => [Hyperf\WebSocketServer\Server::class, 'onClose'],
            ],
        ],
    ],
    'settings'  => [
        Constant::OPTION_ENABLE_COROUTINE      => true,
        Constant::OPTION_WORKER_NUM            => swoole_cpu_num(),
        Constant::OPTION_PID_FILE              => BASE_PATH . '/runtime/hyperf.pid',
        Constant::OPTION_OPEN_TCP_NODELAY      => true,
        Constant::OPTION_MAX_COROUTINE         => 100000,
        Constant::OPTION_OPEN_HTTP2_PROTOCOL   => true,
        Constant::OPTION_MAX_REQUEST           => 100000,
        Constant::OPTION_SOCKET_BUFFER_SIZE    => 2 * 1024 * 1024,
        Constant::OPTION_BUFFER_OUTPUT_SIZE    => 2 * 1024 * 1024,

        // 将 public 替换为上传目录
        Constant::OPTION_DOCUMENT_ROOT         => BASE_PATH . '/storage/public',
        Constant::OPTION_ENABLE_STATIC_HANDLER => true,
    ],
    'callbacks' => [
        Event::ON_WORKER_START => [Hyperf\Framework\Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
        Event::ON_PIPE_MESSAGE => [Hyperf\Framework\Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        Event::ON_WORKER_EXIT  => [Hyperf\Framework\Bootstrap\WorkerExitCallback::class, 'onWorkerExit'],
    ],
];
