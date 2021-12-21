<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository\Interfaces;

interface GameInterface
{
    public function getModel(): \Hyperf\DbConnection\Model\Model;

    public function listPage($pageSize, $filter): \Hyperf\Contract\LengthAwarePaginatorInterface;

    public function createSingleData($data): array;

    public function details($id): array;

    public function roomIdDelGame($roomId);

    public function roomIdGetGameBy($roomId): array;

    public function updateGo($game): array;

    public function updateSingleData($id, $data): array;

    public function clearGame();
}
