<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Service;

use App\Constants\RoomMessage;
use Carbon\Carbon;

class ImService extends Service
{
    public function imJoin($uid, $sid, $data)
    {
        $member = container()->get(MemberService::class)->getMember($uid);
        $io     = socketIo()->of('/room');
        if (! empty($data['id']) && $data['id'] == 'im') {
            $this->joinRoom($io, $sid, $data['id']);
        } else {
            $io->getAdapter()->del($sid, 'im');
        }
        $io->to($data['id'])->emit(RoomMessage::ROOM_MESSAGE_RECEIVE, success_reply('成功', [
            'sender'  => '系统消息',
            'type'    => '1',
            'room_id' => $data['id'],
            'content' => $member['username'] . ' 进来了',
            'time'    => Carbon::now()->toDateTimeString(),
        ]));
    }

    public function imSend($uid, $data)
    {
        $member = container()->get(MemberService::class)->getMember($uid);
        $io     = socketIo()->of('/room');
        $io->to($data['id'])->emit(RoomMessage::ROOM_MESSAGE_RECEIVE, success_reply('成功', [
            'sender'  => $member['username'],
            'type'    => '2',
            'room_id' => $data['id'],
            'content' => $data['content'],
            'time'    => Carbon::now()->toDateTimeString(),
        ]));
    }

    private function joinRoom($io, $sid, string ...$roomId)
    {
        $io->getAdapter()->add($sid, ...$roomId);
    }
}
