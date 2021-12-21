<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository\Interfaces;

interface RoomInterface
{
    public function getModel(): \Hyperf\DbConnection\Model\Model;

    public function listPage($pageSize, $filter): \Hyperf\Contract\LengthAwarePaginatorInterface;

    public function createSingleData($data): array;

    public function details($id): array;

    public function updateSingleData($id, $data): array;

    public function getOnlineList(): array;

    /**
     * @param $uid
     * @param $roomId
     *
     * @return array|false
     */
    public function join($uid, $roomId);

    /**
     * @param $uid
     * @param $roomId
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function leave($uid, $roomId);

    /**
     * @param $uid
     * @param $roomId
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function ready($uid, $roomId);

    /**
     * @param $uid
     * @param $roomId
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function cancelReady($uid, $roomId);

    public function watch($uid, $roomId);

    public function leaveAll($uid): array;

    public function deleteNullAllRoom();

    public function deleteRoom($id);

    public function clearRoom();
}
