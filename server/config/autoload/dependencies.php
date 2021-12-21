<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
return [
    \App\Plugins\Contracts\FileUploadInterface::class  => \App\Plugins\FileUpload::class,
    \App\Plugins\Contracts\LogFileViewInterface::class => \App\Plugins\LogFileView::class,
    \Hyperf\SocketIOServer\SocketIO::class             => \App\Kernel\SocketIOFactory::class,
    \App\Repository\Interfaces\MemberInterface::class  => \App\Repository\Repositories\MemberRepository::class,
    \App\Repository\Interfaces\RoomInterface::class    => \App\Repository\Repositories\RoomRepository::class,
    \App\Repository\Interfaces\GameInterface::class    => \App\Repository\Repositories\GameRepository::class,
];
