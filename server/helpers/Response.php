<?php

declare(strict_types=1);

/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use App\Constants\ResponseCode;
use Hyperf\Utils\Codec\Json;

if (! function_exists('failed')) {
    function failed($message = '请求失败', $data = [], $code = ResponseCode::ERROR): Psr\Http\Message\ResponseInterface
    {
        return respond($code, $message, $data);
    }
}

if (! function_exists('success')) {
    function success($message = '请求成功', $data = [], $code = ResponseCode::SUCCESS): Psr\Http\Message\ResponseInterface
    {
        return respond($code, $message, $data);
    }
}

if (! function_exists('message')) {
    function message($message, $code = ResponseCode::SUCCESS): Psr\Http\Message\ResponseInterface
    {
        return respond($code, $message, []);
    }
}
if (! function_exists('respond')) {
    function respond(
        $code = ResponseCode::SUCCESS,
        $message = '',
        $data = [],
        $http_code = null
    ): Psr\Http\Message\ResponseInterface {
        if ($http_code) {
            return response()
                ->withStatus($http_code)
                ->json([
                    'code'    => $code,
                    'message' => $message,
                    'data'    => $data ?: null,
                ]);
        }
        return response()
            ->json([
                'code'    => $code,
                'message' => $message,
                'data'    => $data ?: null,
            ]);
    }
}

if (! function_exists('reply')) {
    /**
     * @param int $code
     * @param string $message
     * @param array $data
     *
     * @return string
     */
    function reply(int $code = ResponseCode::SUCCESS, string $message = '', array $data = []): string
    {
        return Json::encode([
            'code'    => $code,
            'message' => $message,
            'data'    => $data ?: null,
        ]);
    }
}

if (! function_exists('success_reply')) {
    /**
     * @param string $message
     * @param array $data
     *
     * @return string
     */
    function success_reply(string $message = '成功', array $data = []): string
    {
        return reply(ResponseCode::SUCCESS, $message, $data);
    }
}

if (! function_exists('error_reply')) {
    /**
     * @param string $message
     * @param array $data
     *
     * @return string
     */
    function error_reply(string $message = '失败', array $data = []): string
    {
        return reply(ResponseCode::ERROR, $message, $data);
    }
}
