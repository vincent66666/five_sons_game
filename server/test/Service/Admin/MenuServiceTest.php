<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace HyperfTest\Service\Admin;

use App\Service\Admin\MenuService;
use HyperfTest\HttpTestCase;

/**
 * @internal
 * @coversNothing
 */
class MenuServiceTest extends HttpTestCase
{
    public function testListPage()
    {
        $this->assertTrue(true);
        $service = container()->get(MenuService::class);
        $res     = $service->listPage(5, []);
        $this->assertNotEmpty($res->toArray());
    }

    public function testListChildren()
    {
        $this->assertTrue(true);
        $service = container()->get(MenuService::class);
        $res     = $service->listChildren();
        $this->assertNotEmpty($res);
    }
}
