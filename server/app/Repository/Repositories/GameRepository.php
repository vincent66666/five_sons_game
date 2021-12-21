<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository\Repositories;

use App\Model\Game;
use App\Repository\Interfaces\GameInterface;
use App\Repository\Repository;

class GameRepository extends Repository implements GameInterface
{
    public string $table = Game::class;

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
        return $this->getModel()::query()->find($id)->toArray();
    }

    public function roomIdDelGame($roomId)
    {
        return $this->getModel()::query()->where('room_id', $roomId)->delete();
    }

    public function roomIdGetGameBy($roomId): array
    {
        return $this->getModel()::query()->where('room_id', $roomId)->first()->toArray();
    }

    public function updateGo($game): array
    {
        return $this->updateOneBy(['id' => $game['id']], $game);
    }

    public function updateSingleData($id, $data): array
    {
        return $this->updateOneBy(['id' => $id], $data);
    }

    public function clearGame()
    {
        $this->getModel()::query()->truncate();
    }
}
