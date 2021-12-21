<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ResponseCode extends AbstractConstants
{
    /**
     * @Message("请求错误");
     */
    public const ERROR = 0;

    /**
     * @Message("成功");
     */
    public const SUCCESS = 200;

    /**
     * 验证失败.
     * @Message("验证失败");
     */
    public const UNPROCESSABLE = 422;

    /**
     * 请求频繁
     * @Message("请求频繁");
     */
    public const MAX_REQUEST = 429;

    /**
     * @Message("参数错误")
     */
    public const CODE_ERR_PARAM = 600;

    /**
     * @Message("请求错误");
     */
    public const HTTP_ERROR = 500;

    /**
     * @Message("成功");
     */
    public const HTTP_SUCCESS = 200;

    /**
     * 请求被创建完成，同时新的资源被创建。
     * @Message("The request is created and the new resource is created.");
     */
    public const HTTP_CREATE_ED = 201;

    /**
     * 供处理的请求已被接受，但是处理未完成。
     * @Message("The request for processing has been accepted, but the processing has not been
     *               completed.");
     */
    public const HTTP_ACCEPT_ED = 202;

    /**
     * 供处理的请求已被接受，但是处理未完成。
     * @Message("The request has been successfully processed, but some response headers may be
     *               incorrect because copies of other documents are used.");
     */
    public const HTTP_NON_AUTH_INFORMATION = 203;

    /**
     * 请求已经被成功处理，但是没有返回新文档。浏览器应该继续显示原来的文档。
     * @Message("The request was successfully processed, but no new document was returned. The
     *               browser should continue to display the original document.");
     */
    public const HTTP_NO_CONTENT = 204;

    /**
     * 请求已经被成功处理，但是没有返回新文档。但浏览器应该重置它所显示的内容。用来强制浏览器清除表单输入内容。
     * @Message("The request was successfully processed, but no new document was returned. But the
     *               browser should reset what it displays. Used to force the browser to clear the
     *               form input.");
     */
    public const HTTP_RESET_CONTENT = 205;

    /**
     * 客户发送了一个带有Range头的GET请求，服务器完成了它。
     * @Message("The client sends a GET request with a Range header, and the server completes it.");
     */
    public const HTTP_PARTIAL_CONTENT = 206;

    /**
     * 请求成功，缓存生效.
     * @Message("NO_TMODIFIED");
     */
    public const HTTP_NO_TMODIFIED = 302;

    /**
     * 请求错误，无法解析请求体.
     * @Message("The server failed to understand the request because of a syntax error.");
     */
    public const HTTP_BAD_REQUEST = 400;

    /**
     * 认证失败.
     * @Message("认证失败");
     */
    public const HTTP_UNAUTHORIZED = 401;

    /**
     * 服务器已经接受到请求，但拒绝执行，没有权限.
     * @Message("FORBIDDEN");
     */
    public const HTTP_FORBIDDEN = 403;

    /**
     * 找不到请求的资源.
     * @Message("Not Found");
     */
    public const HTTP_NOT_FOUND = 404;

    /**
     * 方法不允许当前用户访问.
     * @Message("Method Not Allowed");
     */
    public const HTTP_METHOD_NOT_ALLOWED = 405;

    /**
     * 请求资源已过期
     * @Message("GONE");
     */
    public const HTTP_GONE = 410;

    /**
     * 请求体内的类型错误.
     * @Message("MEDIA_TYPE");
     */
    public const HTTP_MEDIA_TYPE = 405;

    /**
     * 验证失败.
     * @Message("验证失败");
     */
    public const HTTP_UNPROCESSABLE = 422;

    /**
     * 无效令牌.
     */
    public const TOKEN_INVALID = 14010;

    /**
     * 无效的声明.
     * @Message("无效的声明")
     */
    public const TOKEN_INVALID_CLAIM = 14011;

    /**
     * 令牌已经在黑名单中.
     * @Message("该令牌已被列入黑名单")
     */
    public const TOKEN_BLACKLIST = 14012;

    /**
     * 令牌已过期
     * @Message("令牌已过期")
     */
    public const TOKEN_EXPIRED = 14013;

    /**
     * 无效的配置.
     * @Message("JWT配置未定义")
     */
    public const TOKEN_INVALID_CONFIG = 14014;

    /**
     * 标头无效，有效载荷无效.
     * @Message("标头无效，有效载荷无效")
     */
    public const TOKEN_PROVIDER = 14015;

    /**
     * 令牌已过期，不支持刷新.
     * @Message("token expired, refresh is not supported")
     */
    public const TOKEN_REFRESH_EXPIRED = 14016;
}
