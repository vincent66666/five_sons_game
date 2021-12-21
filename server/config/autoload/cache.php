<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
return [
    'default' => [
        'driver' => Hyperf\Cache\Driver\RedisDriver::class,
        'packer' => Hyperf\Utils\Packer\PhpSerializerPacker::class,
        'prefix' => 'c:',
    ],
];
