<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Aspect;

use App\Event\SocketIoConnect;
use App\Event\SocketIoDisConnect;
use App\Exception\ErrorResponseException;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\WebSocketServer\Context as WsContext;

/**
 * @Aspect
 */
class SocketIoAspect extends AbstractAspect
{
    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public $classes = [
        'Hyperf\SocketIOServer\SocketIO::onClose',
        'Hyperf\SocketIOServer\SocketIO::onOpen',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $methodName = $proceedingJoinPoint->methodName;
        $args       = $proceedingJoinPoint->getArguments();

        $sidProvider = container()->get(SidProviderInterface::class);
        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理
        try {
            $result = $proceedingJoinPoint->process();
        } catch (Exception $e) {
            throw new ErrorResponseException(500, ' proceedingJoinPoint process errors');
        }
        switch ($methodName) {
            case 'onOpen':
                $request = $args[1];
                $sid     = $sidProvider->getSid($request->fd);
                $uid     = WsContext::get('uid');
                stdLog()->info('Aop Websocket onOpen ' . $sid);
                eventDispatcher()->dispatch(new SocketIoConnect(
                    $uid,
                    $sid,
                    $request->fd
                ));
                break;
            case 'onClose':
            default:
                $uid = WsContext::get('uid');
                $fd  = $args[1];
                $sid = $sidProvider->getSid($fd);
                stdLog()->info('Aop Websocket onClose ' . $sid);
                eventDispatcher()->dispatch(new SocketIoDisConnect(
                    $uid,
                    $sid,
                    $fd
                ));
                break;
        }


        // 在调用后进行某些处理
        return $result;
    }
}
