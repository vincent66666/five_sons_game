<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Traits;

use App\Constants\SocketIo;
use App\Event\SocketIoConnect;
use Carbon\Carbon;
use Hyperf\Utils\Str;
use Hyperf\WebSocketServer\Context as WsContext;

trait SocketIoUtils
{
    public function bindSocketUid($token, $sid, $fd): string
    {
        $token = $this->parseToken($token);
        $uid   = (string) auth('api')->user($token)->getId();
        stdLog()->info(
            'onReceive: ' . json_encode([
                'sid'  => $sid,
                'uid'  => $uid,
                'time' => Carbon::now()->toDateTimeString(),
            ])
        );
        $sidCache = $this->uidGetSid($uid);
        $uidCache = $this->sidGetUid($sid);
        if ($sidCache != $sid || $uidCache != $uid) {
            eventDispatcher()->dispatch(new SocketIoConnect($uid, $sid, $fd));
        }
        return $uid;
    }

    public function getUid()
    {
        return WsContext::get('uid');
    }

    public function getSocketSid(?string $uid = null): string
    {
        $uid ??= $this->getUid();
        return redis()->hGet(SocketIo::HASH_UID, $uid) ?? '0';
    }

    public function sidGetUid($sid): string
    {
        return redis()->hGet(SocketIo::HASH_SID, $sid) ?? '0';
    }

    public function uidGetSid($uid): string
    {
        return redis()->hGet(SocketIo::HASH_UID, $uid) ?? '0';
    }

    public function parseToken($token): ?string
    {
        if (Str::startsWith($token, 'Bearer ')) {
            return Str::substr($token, 7);
        }
        return null;
    }
}
