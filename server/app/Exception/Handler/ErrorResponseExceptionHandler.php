<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Exception\Handler;

use App\Constants\ResponseCode;
use App\Exception\ErrorResponseException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorResponseExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // 判断被捕获到的异常是希望被捕获的异常
        if ($throwable instanceof ErrorResponseException) {
            // 阻止异常冒泡
            $this->stopPropagation();
            return $response->withStatus(ResponseCode::SUCCESS)
                ->withAddedHeader('content-type', 'application/json')
                ->withBody(new SwooleStream((string) failed($throwable->getMessage(), [], $throwable->getCode())));
        }

        // 交给下一个异常处理器
        return $response;
        // 或者不做处理直接屏蔽异常
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理.
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ErrorResponseException;
    }
}
