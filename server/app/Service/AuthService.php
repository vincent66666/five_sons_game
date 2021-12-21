<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Service;

use App\Repository\Interfaces\MemberInterface;

/**
 * Class AuthService.
 *
 * @property MemberInterface $memberRepo
 */
class AuthService extends Service
{
    /**
     * @param $credentials
     * @param $msg
     *
     * @return \App\Model\Member|false|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object
     */
    public function login($credentials, &$msg)
    {
        $member = $this->memberRepo->loginAccount($credentials['username']);
        if (empty($member)) {
            $msg = '该用户不存在！';
            return false;
        }
        if (! jwt('api')->getEncrypter()->check($credentials['password'], $member['password'])) {
            $msg = '密码输入错误！';
            return false;
        }
        return $member;
    }

    /**
     * @param $input
     *
     * @return \App\Model\Member|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model
     */
    public function register($input)
    {
        $input['nickname'] = $input['username'];
        $input['avatar']   = 'uPic/2021_09_29/public/Aff' . random_int(1, 32) . '.png';
        return $this->memberRepo->createSingleData($input);
    }
}
