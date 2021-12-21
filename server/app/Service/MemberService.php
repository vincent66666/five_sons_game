<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Service;

use App\Repository\Interfaces\MemberInterface;

/**
 * @property MemberInterface $memberRepo
 */
class MemberService extends Service
{
    public function getMember($uid)
    {
        return $this->memberRepo->getUserCacheObj($uid);
    }
}
