<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Controller;

use App\Event\LoginSuccessEvent;
use App\Service\AuthService;

/**
 * Class AuthController.
 *
 * @property AuthService $authService
 */
class AuthController extends AbstractController
{
    public function register(): \Psr\Http\Message\ResponseInterface
    {
        $validator = validator([
            'username' => request()->input('username'),
            'password' => request()->input('password'),
        ], [
            'username' => ['required', 'unique:members,username'],
            'password' => ['required', 'between:8,30'],
        ], ['username' => '登录账号', 'password' => '密码']);
        $input     = $validator->validated();
        if (! $member = $this->authService->register($input)) {
            return failed('注册失败');
        }
        if (! $token = auth('api')->login($member)) {
            return failed('登录失败');
        }
        eventDispatcher()->dispatch(new LoginSuccessEvent('api', $member));
        return success('注册成功', [
            'token'             => $token,
            'token_type'        => 'Bearer',
            'expire_in'         => config('auth.guards.api.ttl') * 60,
            'refresh_expire_in' => config('auth.guards.api.refresh_ttl', 60 * 60 * 24 * 7) * 60,
        ]);
    }

    public function login(): \Psr\Http\Message\ResponseInterface
    {
        $validator   = validator([
            'username' => request()->input('username'),
            'password' => request()->input('password'),
        ], [
            'username' => ['required'],
            'password' => ['required'],
        ], ['username' => '登录账号', 'password' => '密码']);
        $credentials = $validator->validated();
        if (! $member = $this->authService->login($credentials, $msg)) {
            return failed('密码失败');
        }
        if (! $token = auth('api')->login($member)) {
            return failed('登录失败');
        }
        eventDispatcher()->dispatch(new LoginSuccessEvent('api', $member));
        return success('登陆成功', [
            'token'             => $token,
            'token_type'        => 'Bearer',
            'expire_in'         => config('auth.guards.api.ttl') * 60,
            'refresh_expire_in' => config('auth.guards.api.refresh_ttl', 60 * 60 * 24 * 7) * 60,
        ]);
    }

    public function user(): \Psr\Http\Message\ResponseInterface
    {
        $user = auth('api')->user();
        return success('成功', [
            'id'              => $user->id,
            'avatar'          => fileUpload()->url($user->avatar),
            'username'        => $user->username,
            'nickname'        => $user->nickname,
            'last_login_time' => $user->last_login_time,
        ]);
    }

    public function logout(): \Psr\Http\Message\ResponseInterface
    {
        auth('api')->logout();
        return success('Successfully logged out');
    }

    public function refresh(): \Psr\Http\Message\ResponseInterface
    {
        return success('刷新成功', [
            'token'             => auth('api')->refresh(),
            'token_type'        => 'Bearer',
            'expire_in'         => config('auth.guards.api.ttl') * 60,
            'refresh_expire_in' => config('auth.guards.api.refresh_ttl', 60 * 60 * 24 * 7) * 60,
        ]);
    }
}
