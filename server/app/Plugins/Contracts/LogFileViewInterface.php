<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Plugins\Contracts;

use Hyperf\Paginator\Paginator;
use Hyperf\Utils\Collection;

interface LogFileViewInterface
{
    /**
     * @return Collection|Paginator|string
     */
    public function getLogListForPage();

    /**
     * @return Collection|Paginator|string
     */
    public function getDetailForPage();

    /**
     * @return int
     */
    public function getLogListTotal(): int;

    /**
     * @return int
     */
    public function getDetailTotal(): int;
}
