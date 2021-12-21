<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Controller;

use App\Traits\SocketIoUtils;
use Hyperf\Di\Annotation\Inject;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\Socket;

class RoomController extends BaseNamespace
{
    use SocketIoUtils;

    /**
     * @Inject
     * @var \App\Service\RoomService
     */
    private \App\Service\RoomService $roomService;

    /**
     * @Inject
     * @var \App\Service\GameService
     */
    private \App\Service\GameService $gameService;

    /**
     * @Inject
     * @var \App\Service\ImService
     */
    private \App\Service\ImService $imService;

    /**
     * @Event("store")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onStore(Socket $socket, $data): string
    {
        $validator = validator(
            ['title' => $data['title']],
            ['title' => ['required', 'unique:rooms,title']],
            ['title' => '标题']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $data = $this->roomService->create($this->getUid(), $socket->getSid(), $data['title']);
        return success_reply('成功', $data);
    }

    /**
     * @Event("lists")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onLists(Socket $socket, $data): string
    {
        // 应答
        $roomList = $this->roomService->lists();
        return success_reply('成功', $roomList);
    }

    /**
     * @Event("show")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onShow(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required', 'exists:rooms,id']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        // 应答
        $room = $this->roomService->show($data['id']);
        return success_reply('成功', $room);
    }

    /**
     * @Event("join")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onJoin(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required', 'exists:rooms,id']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->roomService->join($this->getUid(), $socket->getSid(), (string) $data['id']);
        return success_reply('成功', $data);
    }

    /**
     * @Event("watch")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onWatch(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required', 'exists:rooms,id']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->roomService->watch($this->getUid(), $socket->getSid(), (string) $data['id']);
        return success_reply('成功', $data);
    }

    /**
     * @Event("leave")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onLeave(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required', 'exists:rooms,id']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->roomService->leave($this->getUid(), $socket->getSid(), (string) $data['id']);
        return success_reply();
    }

    /**
     * @Event("go")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onGo(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id'], 'x' => $data['x'], 'y' => $data['y']],
            [
                'id' => ['required', 'exists:rooms,id'],
                'x'  => ['required'],
                'y'  => ['required'],
            ],
            ['id' => '房间', 'x' => 'x轴', 'y' => 'y轴']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        if (! $this->gameService->go($this->getUid(), $socket->getSid(), $data)) {
            return error_reply();
        }
        return success_reply();
    }

    /**
     * @Event("ready")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onReady(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required', 'exists:rooms,id']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->roomService->ready($this->getUid(), (string) $data['id']);
        return success_reply();
    }

    /**
     * @Event("cancelReady")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onCancelReady(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required', 'exists:rooms,id']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->roomService->cancelReady($this->getUid(), (string) $data['id']);
        return success_reply();
    }

    /**
     * @Event("imJoin")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onImJoin(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id']],
            ['id' => ['required']],
            ['id' => '房间']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->imService->imJoin($this->getUid(), $socket->getSid(), $data);
        return success_reply('成功', $data);
    }

    /**
     * @Event("imSend")
     * @param Socket $socket
     * @param $data
     *
     * @return string
     */
    public function onImSend(Socket $socket, $data): string
    {
        $validator = validator(
            ['id' => $data['id'], 'content' => $data['content']],
            ['id' => ['required'], 'content' => ['required']],
            ['id' => '房间', 'content' => '内容']
        );
        if ($validator->fails()) {
            return error_reply($validator->errors()->first());
        }
        $this->imService->imSend($this->getUid(), $data);
        return success_reply('成功', $data);
    }
}
