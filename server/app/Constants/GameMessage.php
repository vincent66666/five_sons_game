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
class GameMessage extends AbstractConstants
{
    /**
     * @Message("游戏详情");
     */
    public const GAME_SHOW = 'game_show';
}
