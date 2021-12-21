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
class GameStatus extends AbstractConstants
{
    /**
     * @Message("等待开始")
     */
    public const WAIT_START = 1;

    /**
     * @Message("游戏中")
     */
    public const GAMING = 2;
}
