<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Service;

use App\Constants\GameMessage;
use App\Constants\GameStatus;
use App\Constants\RoomMessage;
use App\Repository\Interfaces\RoomInterface;

/**
 * @property RoomInterface $roomRepo
 * @property GameService $gameService
 * @property MemberService $memberService
 */
class RoomService extends Service
{
    public function create($uid, $sid, $title): array
    {
        $data = [
            'title'            => $title,
            'create_uid'       => $uid,
            'player1_id'       => $uid,
            'watch_member_ids' => [],
        ];
        // 创建房间
        $room = $this->roomRepo->createSingleData($data);
        // 加入房间
        $this->join($uid, $sid, (string) $room['id']);
        return $room;
    }

    public function join($uid, $sid, $roomId): bool
    {
        if (! $returnRoom = $this->roomRepo->join($uid, $roomId)) {
            return false;
        }
        $this->joinRoom($uid, $sid, $roomId);
        defer(function () use ($returnRoom) {
            // 推送房间列表
            $this->pushList();

            socketIo()->of('/room')->to($returnRoom['id'])->emit(
                RoomMessage::ROOM_SHOW,
                success_reply('成功', $this->show($returnRoom['id']))
            );
        });
        return true;
    }

    public function lists(): array
    {
        $roomList = $this->roomRepo->getOnlineList();
        foreach ($roomList as $index => $item) {
            $roomList[$index]['person'] = '1';
            if ($item['player1_id'] > 0 && $item['player2_id'] > 0) {
                $roomList[$index]['person'] = '2';
            }
        }
        return $roomList;
    }

    public function show($roomId): array
    {
        return $this->roomRepo->details($roomId);
    }

    public function ready($uid, $roomId)
    {
        $room = $this->roomRepo->ready($uid, $roomId);
        $game = [];
        if ($room['status'] == GameStatus::GAMING) {
            //创建游戏
            $game = $this->gameService->store($roomId);
        }
        defer(function () use ($roomId, $game) {
            socketIo()->of('/room')->to($roomId)->emit(
                RoomMessage::ROOM_SHOW,
                success_reply('成功', $this->show($roomId))
            );

            socketIo()->of('/room')->to($roomId)->emit(
                GameMessage::GAME_SHOW,
                success_reply('成功', ['game' => $game])
            );
            // 推送房间列表
            $this->pushList();
        });
    }

    public function cancelReady($uid, $roomId)
    {
        $this->roomRepo->cancelReady($uid, $roomId);

        defer(function () use ($roomId) {
            socketIo()->of('/room')->to($roomId)->emit(
                RoomMessage::ROOM_SHOW,
                success_reply('成功', $this->show($roomId))
            );
            // 推送房间列表
            $this->pushList();
        });
    }

    public function watch($uid, $sid, $roomId)
    {
        $room = $this->roomRepo->watch($uid, $roomId);
        $game = null;
        if ($room['status'] == GameStatus::GAMING) {
            $game = $this->gameService->roomIdGetGameBy($roomId);
        }
        $this->joinRoom($uid, $sid, $roomId);
        defer(function () use ($roomId, $game) {
            socketIo()->of('/room')->to($roomId)->emit(
                RoomMessage::ROOM_SHOW,
                success_reply('成功', $this->show($roomId))
            );
            socketIo()->of('/room')->to($roomId)->emit(
                GameMessage::GAME_SHOW,
                success_reply('成功', ['game' => $game])
            );
            // 推送房间列表
            $this->pushList();
        });
    }

    public function leave($uid, $sid, $roomId, $isAll = false)
    {
        $room = $this->roomRepo->leave($uid, $roomId);
        if ($room['player1_id'] == 0 && $room['player2_id'] == 0) {
            $this->roomRepo->deleteRoom($roomId);
            $this->gameService->deleteGame($roomId);
            socketIo()->of('/room')->to($roomId)->emit(RoomMessage::ROOM_DESTROY, success_reply());
        }
        if ($room['status'] == GameStatus::GAMING) {
            $winnerMemberId = $room['player1_id'];
            if ($room['player1_id'] === 0) {
                $winnerMemberId = $room['player2_id'];
            }
            $this->gameService->deleteGame($roomId);
            $winner = $this->memberService->getMember($winnerMemberId);
            socketIo()->of('/room')->to($roomId)->emit(
                GameMessage::GAME_SHOW,
                success_reply('', ['winner' => $winner])
            );
            $this->roomRepo->updateSingleData($roomId, [
                'player1_ready' => 0,
                'player2_ready' => 0,
                'status'        => 1,
            ]);
        }
        if ($isAll === false) {
            $io = socketIo()->of('/room');
            $io->getAdapter()->del($sid, $roomId);
        }

        defer(function () use ($roomId) {
            socketIo()->of('/room')->to($roomId)->emit(
                RoomMessage::ROOM_SHOW,
                success_reply('成功', $this->show($roomId))
            );
            // 推送房间列表
            $this->pushList();
        });
    }

    public function leaveAll($uid, $sid)
    {
        $rooms = $this->roomRepo->leaveAll($uid);
        foreach ($rooms as $room) {
            $this->leave($uid, $sid, $room, true);
        }
    }

    public function clearRoom()
    {
        $this->roomRepo->clearRoom();
    }

    public function pushList()
    {
        // 推送房间列表
        $roomLists = $this->lists();
        socketIo()->of('/room')->emit(RoomMessage::ROOM_LISTS, success_reply('成功', $roomLists));
    }

    private function joinRoom($uid, $sid, string ...$roomId)
    {
//        $member = container()->get(MemberService::class)->getMember($uid);
        $io     = socketIo()->of('/room');
        $io->getAdapter()->add($sid, ...$roomId);
    }
}
