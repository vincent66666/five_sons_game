<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\SocketIOServer\Collector\SocketIORouter;

SocketIORouter::addNamespace('/handshake', \App\Controller\HandShakeController::class);
SocketIORouter::addNamespace('/room', \App\Controller\RoomController::class);
