<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Middleware;

use Hyperf\Utils\Context;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
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
        $origin   = $request->getHeaderLine('origin');
        $Referer  = $request->getHeaderLine('referer');
        $response = Context::get(ResponseInterface::class);
        if (Str::contains($Referer, 'http://localhost:8081') || Str::contains($origin, 'http://localhost:8081')) {
            $response = $response->withHeader('Access-Control-Allow-Origin', 'http://localhost:8081')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                // Headers 可以根据实际情况进行改写。
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization'
                );
            Context::set(ResponseInterface::class, $response);
            if ($request->getMethod() == 'OPTIONS') {
                return $response;
            }
        }
        return $handler->handle($request);
    }
}
