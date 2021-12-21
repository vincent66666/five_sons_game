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
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 500;

    /**
     * @Message("Error！")
     */
    public const ERROR = 0;

    /**
     * @Message("TOKEN异常")
     */
    public const TOKEN_ERROR = 1;

    /**
     * @Message("账号：%s 已被禁用")
     */
    public const ADMIN_USER_DISABLE = 2;

    /**
     * @Message("账号：%s 不存在")
     */
    public const ADMIN_USER_NOT_EXIST = 3;

    /**
     * @Message("密码错误")
     */
    public const ADMIN_USER_PASSWORD = 4;

    /**
     * @Message("当前登录设备与账号：%s 绑定的设备不一致")
     */
    public const ADMIN_USER_DEVICE_NUMBER = 5;

    /**
     * @Message("账号：%s 绑定的设备失败")
     */
    public const ADMIN_USER_BIND_DEVICE_NUMBER = 6;
}
