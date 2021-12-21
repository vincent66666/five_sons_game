<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Exception\Handler;

use App\Constants\ResponseCode;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf(
            '%s[%s] in %s',
            $throwable->getMessage(),
            $throwable->getLine(),
            $throwable->getFile()
        ));
        $this->logger->error($throwable->getTraceAsString());

        $msg  = config('app_env') == 'local' ? $throwable->getMessage() : 'Internal Server Error.';
        $code = config('app_env') == 'local' ? $throwable->getCode() : 500;

        return $response->withStatus(ResponseCode::SUCCESS)
            ->withAddedHeader('content-type', 'application/json')
            ->withBody(new SwooleStream((string) failed($msg, [], $code)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
