<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Kernel;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\SocketIOServer\Parser\Decoder;
use Hyperf\SocketIOServer\Parser\Encoder;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\SocketIOServer\SocketIO;
use Hyperf\WebSocketServer\Sender;
use Psr\Container\ContainerInterface;

class SocketIOFactory
{
    public function __invoke(ContainerInterface $container): SocketIO
    {
        $io = new SocketIO(
            $container->get(StdoutLoggerInterface::class),
            $container->get(Sender::class),
            $container->get(Decoder::class),
            $container->get(Encoder::class),
            $container->get(SidProviderInterface::class)
        );
        // 重写 pingTimeout 参数
        $io->setPingTimeout(10000);
        return $io;
    }
}
