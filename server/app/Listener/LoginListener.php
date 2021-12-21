<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Listener;

use App\Event\LoginSuccessEvent;
use App\Model\Member;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class LoginListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            LoginSuccessEvent::class,
        ];
    }

    public function process(object $event)
    {
        $guard = $event->guard;
        switch ($guard) {
            case 'api':
                /** @var Member $user */
                $user                  = $event->user;
                $user->last_login_time = date('Y-m-d H:i:s');
                $user->last_login_ip   = getClientIp();
                $user->save();
                break;
            case 'admin':
            default:
                /* $user = $event->user; */
                break;
        }
    }
}
