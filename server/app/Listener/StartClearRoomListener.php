<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Listener;

use App\Service\GameService;
use App\Service\RoomService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class StartClearRoomListener implements ListenerInterface
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
        $roomService = $this->container->get(RoomService::class);
        $roomService->clearRoom();
        $gameService = $this->container->get(GameService::class);
        $gameService->clearGame();
    }
}
