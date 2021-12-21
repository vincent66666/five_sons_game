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
class SocketIo extends AbstractConstants
{
    public const HASH_UID = 'hash:socket:uid';

    public const HASH_SID = 'hash:socket:sid';
}
