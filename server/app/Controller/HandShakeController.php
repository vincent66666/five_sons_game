<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Controller;

use App\Traits\SocketIoUtils;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\Socket;

class HandShakeController extends BaseNamespace
{
    use SocketIoUtils;

    /**
     * @Event("receive")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onReceive(Socket $socket, $data): string
    {
        $uid = $this->bindSocketUid($data['token'], $socket->getSid(), $socket->getFd());
        // 应答
        return success_reply('签到成功', ['uid' => $uid, 'receive_time' => carbon_string(time())]);
    }
}
