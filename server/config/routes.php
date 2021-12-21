<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\HttpServer\Router\Router;

reloadRoute();
Router::addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/oss/callback', 'App\Controller\FilesystemController@callback');
Router::addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/check_port', function () {
    return success();
});
