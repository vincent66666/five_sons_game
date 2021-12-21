<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Event;

class LoginSuccessEvent
{
    // 建议这里定义成 public 属性，以便监听器对该属性的直接使用，或者你提供该属性的 Getter
    public string $guard;

    public object $user;

    public function __construct($guard, $user)
    {
        $this->guard = $guard;
        $this->user  = $user;
    }
}
