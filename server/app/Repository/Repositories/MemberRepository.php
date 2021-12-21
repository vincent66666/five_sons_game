<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository\Repositories;

use App\Model\Member;
use App\Repository\Interfaces\MemberInterface;
use App\Repository\Repository;

class MemberRepository extends Repository implements MemberInterface
{
    public string $table = Member::class;

    /**
     * @param $username
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|Member|object
     */
    public function loginAccount($username)
    {
        return $this->getModel()::query()->where('username', $username)->first();
    }

    public function listPage($pageSize, $filter): \Hyperf\Contract\LengthAwarePaginatorInterface
    {
        return $this->getModel()::query()
            ->when($filter, function ($query) use ($filter) {
                if (! empty($filter['id'])) {
                    $query->where('id', $filter['id']);
                }
            })
            ->paginate($pageSize);
    }

    /**
     * @param $data
     *
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|Member
     */
    public function createSingleData($data)
    {
        $data['password'] = jwt('api')->getEncrypter()->signature($data['password']);
        return $this->getModel()::query()->create($data);
    }

    public function details($id): array
    {
        return $this->getModel()::query()->find($id)->toArray();
    }

    public function updateSingleData($id, $data): array
    {
        return $this->updateOneBy(['id' => $id], $data);
    }

    public function getUserCacheObj($userId)
    {
        return $this->getModel()::findFromCache($userId);
    }
}
