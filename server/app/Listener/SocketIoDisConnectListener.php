<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Listener;

use App\Constants\SocketIo;
use App\Event\SocketIoDisConnect;
use Carbon\Carbon;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\WebSocketServer\Context as WsContext;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class SocketIoDisConnectListener implements ListenerInterface
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
        $uid    = $event->uid;
        $sid    = $event->sid;
        $fd     = $event->fd;
        stdLog()->info('onDisConnect: ' . json_encode(['sid' => $sid, 'uid' => $uid, 'time' => Carbon::now()->toDateTimeString()]));
        $sidCache = redis()->hGet(SocketIo::HASH_UID, $uid);
        if ($sidCache !== $sid) {
            redis()->hDel(SocketIo::HASH_SID, $sidCache);
        }
        redis()->hDel(SocketIo::HASH_UID, $uid);
        redis()->hDel(SocketIo::HASH_SID, $sid);
        WsContext::destroy('uid');
    }
}
