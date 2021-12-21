<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository\Repositories;

use App\Model\Room;
use App\Repository\Interfaces\RoomInterface;
use App\Repository\Repository;
use Hyperf\DbConnection\Db;

class RoomRepository extends Repository implements RoomInterface
{
    public string $table = Room::class;

    public function listPage($pageSize, $filter): \Hyperf\Contract\LengthAwarePaginatorInterface
    {
        return $this->getModel()::query()
            ->when($filter, function ($query) {
            })
            ->paginate($pageSize);
    }

    public function createSingleData($data): array
    {
        return $this->createBy($data);
    }

    public function details($id): array
    {
        $room = $this->getModel()::query()->with([
            'player1'     => function ($query) {
                $query->select(['id', 'username', Db::raw('avatar as avatar_url')]);
            },
            'player2'     => function ($query) {
                $query->select(['id', 'username', Db::raw('avatar as avatar_url')]);
            },
            'create_user' => function ($query) {
                $query->select(['id', 'username', Db::raw('avatar as avatar_url')]);
            },
        ])->find($id, ['*', Db::raw('status as status_text')]);
        if (empty($room['id'])) {
            return [];
        }
        $data                     = $room->toArray();
        $data['watchMemberIds']   = $data['watch_member_ids'] ?? [];
        $data['watchMemberInfos'] = [];
        return $data;
    }

    public function updateSingleData($id, $data): array
    {
        return $this->updateOneBy(['id' => $id], $data);
    }

    public function getOnlineList(): array
    {
        return $this->getAllBy(
            [
                'with' => [
                    'player1'     => function ($query) {
                        $query->select(['id', 'username']);
                    },
                    'create_user' => function ($query) {
                        $query->select(['id', 'username']);
                    },
                ],
            ],
            [
                'id',
                'title',
                'status',
                Db::raw('status as status_text'),
                'player1_id',
                'player2_id',
                'create_uid',
                'updated_at',
                'created_at',
            ],
            ['created_at' => 'DESC']
        );
    }

    public function join($uid, $roomId)
    {
        $room = $this->getModel()::query()->find($roomId);
        if ($room['player1_id'] > 0 && $room['player2_id'] > 0) {
            return false;
        }
        if ($room['player1_id'] == $uid || $room['player2_id'] == $uid) {
            return $room->toArray();
        }
        if ($room['player1_id'] == '0') {
            $room->player1_id = $uid;
        } else {
            $room->player2_id = $uid;
        }
        $room->save();
        return $room->toArray();
    }

    /**
     * @param $uid
     * @param $roomId
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function leave($uid, $roomId)
    {
        $room = $this->getModel()::query()->find($roomId);
        if ($room['player1_id'] == $uid) {
            $room['player1_id']    = 0;
            $room['player1_ready'] = 0;
        } elseif ($room['player2_id'] == $uid) {
            $room['player2_id']    = 0;
            $room['player2_ready'] = 0;
        } else {
            $watch_member_ids = $room['watch_member_ids'];
            $uidIndex         = array_search($uid, $watch_member_ids, false);
            if ($uidIndex !== false) {
                unset($watch_member_ids[$uidIndex]);
                $watch_member_ids         = array_values($watch_member_ids);
                $room['watch_member_ids'] = $watch_member_ids;
            }
        }
        $room->save();
        return $room;
    }

    /**
     * @param $uid
     * @param $roomId
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function ready($uid, $roomId)
    {
        $room = $this->getModel()::query()->find($roomId);
        if ($room['player1_id'] == $uid) {
            $room['player1_ready'] = 1;
        }
        if ($room['player2_id'] == $uid) {
            $room['player2_ready'] = 1;
        }
        if ($room['player1_ready'] == 1 && $room['player2_ready'] == 1) {
            $room['status'] = 2;
        }
        $room->save();
        return $room;
    }

    /**
     * @param $uid
     * @param $roomId
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function cancelReady($uid, $roomId)
    {
        $room = $this->getModel()::query()->find($roomId);
        if ($room['player1_id'] == $uid) {
            $room['player1_ready'] = 0;
        }
        if ($room['player2_id'] == $uid) {
            $room['player2_ready'] = 0;
        }
        $room['status'] = 1;
        $room->save();
        return $room;
    }

    public function watch($uid, $roomId)
    {
        $room = $this->getModel()::query()->find($roomId);
        if (in_array($uid, $room['watch_member_ids'], false)) {
            return $room;
        }
        $watch_member_ids         = $room['watch_member_ids'];
        $watch_member_ids[]       = $uid;
        $room['watch_member_ids'] = $watch_member_ids;
        $room->save();
        return $room;
    }

    public function leaveAll($uid): array
    {
        $player1 = $this->getModel()::query()
            ->where('player1_id', $uid)
            ->get(['id']);
        $id1     = [];
        if ($player1->isNotEmpty()) {
            $id1 = $player1->pluck('id')->toArray();
        }

        $id2     = [];
        $player2 = $this->getModel()::query()
            ->where('player2_id', $uid)
            ->get(['id']);
        if ($player2->isNotEmpty()) {
            $id2 = $player2->pluck('id')->toArray();
        }
        return array_merge($id1, $id2);
    }

    public function deleteNullAllRoom()
    {
        $this->getModel()::query()
            ->where('player1_id', 0)
            ->where('player2_id', 0)
            ->delete();
    }

    public function deleteRoom($id)
    {
        $this->getModel()::query()
            ->where('id', $id)
            ->delete();
    }

    public function clearRoom()
    {
        $this->getModel()::query()->truncate();
    }
}
