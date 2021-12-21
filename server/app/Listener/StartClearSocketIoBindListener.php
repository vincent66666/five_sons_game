<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Listener;

use App\Constants\SocketIo;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class StartClearSocketIoBindListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            MainWorkerStart::class,
        ];
    }

    public function process(object $event)
    {
        redis()->del(SocketIo::HASH_UID);
        redis()->del(SocketIo::HASH_SID);
    }
}
