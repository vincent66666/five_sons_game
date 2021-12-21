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
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\SimpleJwt\Exceptions\InvalidTokenException;
use Qbhy\SimpleJwt\Exceptions\SignatureException;
use Qbhy\SimpleJwt\Exceptions\TokenBlacklistException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;
use Qbhy\SimpleJwt\Exceptions\TokenNotActiveException;
use Qbhy\SimpleJwt\Exceptions\TokenProviderException;
use Qbhy\SimpleJwt\Exceptions\TokenRefreshExpiredException;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // TODO: Implement handle() method.
        if (
            $throwable instanceof UnauthorizedException
            || $throwable instanceof InvalidTokenException
            || $throwable instanceof SignatureException
            || $throwable instanceof TokenBlacklistException
            || $throwable instanceof TokenExpiredException
            || $throwable instanceof TokenNotActiveException
            || $throwable instanceof TokenProviderException
            || $throwable instanceof TokenRefreshExpiredException
        ) {
            $message = $throwable->getMessage();
            // 阻止异常冒泡
            $this->stopPropagation();
            $codeData = [
                InvalidTokenException::class        => ResponseCode::TOKEN_INVALID,
                SignatureException::class           => ResponseCode::TOKEN_INVALID_CLAIM,
                TokenBlacklistException::class      => ResponseCode::TOKEN_BLACKLIST,
                TokenExpiredException::class        => ResponseCode::TOKEN_EXPIRED,
                TokenNotActiveException::class      => ResponseCode::TOKEN_INVALID_CONFIG,
                TokenProviderException::class       => ResponseCode::TOKEN_PROVIDER,
                TokenRefreshExpiredException::class => ResponseCode::TOKEN_REFRESH_EXPIRED,
            ];

            $responseCode = $codeData[$throwable->getPrevious() ? get_class($throwable->getPrevious()) : null] ?? ResponseCode::TOKEN_INVALID;
            if (! $msg = ResponseCode::getMessage($responseCode)) {
                $msg = $message;
            }

            // 格式化输出
            $data = json_encode([
                'code'    => $responseCode,
                'message' => $msg,
                'data'    => [],
            ], JSON_UNESCAPED_UNICODE);
            return $response
                ->withStatus($throwable->getCode())
                ->withAddedHeader('content-type', 'application/json')
                ->withBody(new SwooleStream($data));
        }
        // 交给下一个异常处理器
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        // TODO: Implement isValid() method.
        if (
            $throwable instanceof UnauthorizedException
            || $throwable instanceof InvalidTokenException
            || $throwable instanceof SignatureException
            || $throwable instanceof TokenBlacklistException
            || $throwable instanceof \Qbhy\SimpleJwt\Exceptions\TokenExpiredException
            || $throwable instanceof TokenNotActiveException
            || $throwable instanceof TokenProviderException
            || $throwable instanceof TokenRefreshExpiredException
        ) {
            return true;
        }
        return false;
    }
}
