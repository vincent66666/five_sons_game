<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Middleware;

use Hyperf\Utils\Str;
use Hyperf\WebSocketServer\Context as WsContext;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WebSocketAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 伪代码，通过 isAuth 方法拦截握手请求并实现权限检查
        if (! $this->isAuth($request)) {
            return $this->container->get(\Hyperf\HttpServer\Contract\ResponseInterface::class)->raw('Forbidden');
        }
        return $handler->handle($request);
    }

    public function isAuth(ServerRequestInterface $request): bool
    {
        $params = $request->getQueryParams();
        if (! isset($params['token'])) {
            stdLog()->info('WebSocket Auth error token获取失败');
            return false;
        }
        $token = $this->parseToken($params['token']);
        if (! auth('api')->check($token)) {
            return false;
        }
        $uid = auth('api')->user($token)->getId();
        WsContext::set('uid', $uid);
        return true;
    }

    public function parseToken($token): ?string
    {
        if (Str::startsWith($token, 'Bearer ')) {
            return Str::substr($token, 7);
        }
        return null;
    }
}
