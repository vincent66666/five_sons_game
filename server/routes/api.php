<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\HttpServer\Router\Router;

Router::post('/api/login', 'App\Controller\AuthController@login', ['router_name' => '登录接口']);
Router::post('/api/register', 'App\Controller\AuthController@register', ['router_name' => '创建账号']);
Router::addGroup('/api', function () {
    Router::get('/logout', 'App\Controller\AuthController@logout', ['router_name' => '退出登录接口']);
    Router::get('/user', 'App\Controller\AuthController@user', ['router_name' => '获取信息']);
    Router::get('/refresh', 'App\Controller\AuthController@refresh', ['router_name' => '刷新登录token']);
}, ['middleware' => [\App\Middleware\AuthMiddleware::class]]);
