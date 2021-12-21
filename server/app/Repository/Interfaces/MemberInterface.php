<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository\Interfaces;

use App\Model\Member;

interface MemberInterface
{
    public function getModel(): \Hyperf\DbConnection\Model\Model;

    public function listPage($pageSize, $filter): \Hyperf\Contract\LengthAwarePaginatorInterface;

    /**
     * @param $data
     *
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|Member
     */
    public function createSingleData($data);

    public function details($id): array;

    public function updateSingleData($id, $data): array;

    /**
     * @param $username
     *
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|Member|object
     */
    public function loginAccount($username);

    public function getUserCacheObj($userId);
}
