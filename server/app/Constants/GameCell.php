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
class GameCell extends AbstractConstants
{
    /**
     * @Message("空")
     */
    public const NONE = 0;

    /**
     * @Message("黑棋")
     */
    public const BLACK_PIECE = 1;

    /**
     * @Message("白棋")
     */
    public const WHITE_PIECE = 2;
}
