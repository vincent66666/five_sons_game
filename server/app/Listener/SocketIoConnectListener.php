<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Listener;

use App\Constants\SocketIo;
use App\Event\SocketIoConnect;
use Carbon\Carbon;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class SocketIoConnectListener implements ListenerInterface
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
            SocketIoConnect::class,
        ];
    }

    public function process(object $event)
    {
        $uid    = $event->uid;
        $sid    = $event->sid;
        $fd     = $event->fd;
        stdLog()->info('onConnect: ' . json_encode(['sid' => $sid, 'uid' => $uid, 'time' => Carbon::now()->toDateTimeString()]));
        $oldSid = redis()->hGet(SocketIo::HASH_UID, $uid);
        redis()->multi();
        if ($oldSid) {
            //解除之前的关系
            redis()->hDel(SocketIo::HASH_UID, $uid);
            redis()->hDel(SocketIo::HASH_SID, $oldSid);
            unset($oldSid);
        }
        redis()->hSet(SocketIo::HASH_SID, $sid, $uid);
        redis()->hSet(SocketIo::HASH_UID, $uid, $sid);
        redis()->exec();
    }
}
