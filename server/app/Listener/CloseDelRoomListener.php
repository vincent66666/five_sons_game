<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Listener;

use App\Event\SocketIoDisConnect;
use App\Service\RoomService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class CloseDelRoomListener implements ListenerInterface
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
            SocketIoDisConnect::class,
        ];
    }

    public function process(object $event)
    {
        $uid         = $event->uid;
        $sid         = $event->sid;
        $fd          = $event->fd;
        $roomService = $this->container->get(RoomService::class);
        $roomService->leaveAll($uid, $sid);
    }
}
