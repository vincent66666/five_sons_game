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
class RoomMessage extends AbstractConstants
{
    /**
     * @Message("房间接收消息");
     */
    public const ROOM_MESSAGE_RECEIVE = 'message_receive';

    /**
     * @Message("房间发送消息");
     */
    public const ROOM_MESSAGE_SEND = 'message_send';

    /**
     * @Message("房间列表");
     */
    public const ROOM_LISTS = 'lists';

    /**
     * @Message("房间详情");
     */
    public const ROOM_SHOW = 'show';

    /**
     * @Message("离开房间");
     */
    public const ROOM_LEAVE = 'leave';

    /**
     * @Message("销毁房间");
     */
    public const ROOM_DESTROY = 'destroy';

    /**
     * @Message("创建房间");
     */
    public const ROOM_CREATE = 'store';
}
