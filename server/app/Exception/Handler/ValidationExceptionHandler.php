<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Exception\Handler;

use App\Constants\ResponseCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof ValidationException) {
            $this->stopPropagation();
            /** @var ValidationException $throwable */
            $body = $throwable->validator->errors()->first();
            logger()->info(failed($body));
            return $response->withStatus(ResponseCode::HTTP_SUCCESS)
                ->withAddedHeader('content-type', 'application/json')
                ->withBody(new SwooleStream((string) failed($body)));
        }
        // 交给下一个异常处理器
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
