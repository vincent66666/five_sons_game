<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Qbhy\SimpleJwt\Encoders;
use Qbhy\SimpleJwt\EncryptAdapters as Encrypter;

/*
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */
return [
    'default' => [
        'guard'    => 'admin',
        'provider' => 'admin_users',
    ],
    'guards' => [
        'admin' => [
            'driver'   => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'admin_users',

            /*
             * 以下是 simple-jwt 配置
            * 必填
            * jwt 服务端身份标识
            */
            'secret' => env('SIMPLE_JWT_SECRET'),

            /*
             * 可选配置
             * jwt 默认头部token使用的字段
             */
            'header_name' => env('JWT_HEADER_NAME', 'Authorization'),

            /*
             * 可选配置
             * jwt 生命周期，单位分钟
             */
            'ttl' => (int) env('SIMPLE_JWT_TTL', 60 * 60),

            /*
             * 可选配置
             * 允许过期多久以内的 token 进行刷新
             */
            'refresh_ttl' => (int) env('SIMPLE_JWT_REFRESH_TTL', 60 * 60 * 24 * 7),

            /*
             * 可选配置
             * 默认使用的加密类
             */
            'default' => Encrypter\PasswordHashEncrypter::class,

            /*
             * 可选配置
             * 加密类必须实现 Qbhy\SimpleJwt\Interfaces\Encrypter 接口
             */
            'drivers' => [
                Encrypter\PasswordHashEncrypter::alg() => Encrypter\PasswordHashEncrypter::class,
                Encrypter\CryptEncrypter::alg()        => Encrypter\CryptEncrypter::class,
                Encrypter\SHA1Encrypter::alg()         => Encrypter\SHA1Encrypter::class,
                Encrypter\Md5Encrypter::alg()          => Encrypter\Md5Encrypter::class,
            ],

            /*
             * 可选配置
             * 编码类
             */
            'encoder' => new Encoders\Base64UrlSafeEncoder(),
            //            'encoder' => new Encoders\Base64Encoder(),

            /*
             * 可选配置
             * 缓存类
             */
            //            'cache' => new \Doctrine\Common\Cache\FilesystemCache(sys_get_temp_dir()),
            // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
            'cache' => function () {
                return make(\Qbhy\HyperfAuth\HyperfRedisCache::class);
            },

            /*
             * 可选配置
             * 缓存前缀
             */
            'prefix' => env('SIMPLE_JWT_PREFIX', 'default'),
        ],
        'api' => [
            'driver'   => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'members',

            /*
             * 以下是 simple-jwt 配置
            * 必填
            * jwt 服务端身份标识
            */
            'secret' => env('SIMPLE_JWT_SECRET'),

            /*
             * 可选配置
             * jwt 默认头部token使用的字段
             */
            'header_name' => env('JWT_HEADER_NAME', 'Authorization'),

            /*
             * 可选配置
             * jwt 生命周期，单位分钟
             */
            'ttl' => (int) env('SIMPLE_JWT_TTL', 60 * 60),

            /*
             * 可选配置
             * 允许过期多久以内的 token 进行刷新
             */
            'refresh_ttl' => (int) env('SIMPLE_JWT_REFRESH_TTL', 60 * 60 * 24 * 7),

            /*
             * 可选配置
             * 默认使用的加密类
             */
            'default' => Encrypter\PasswordHashEncrypter::class,

            /*
             * 可选配置
             * 加密类必须实现 Qbhy\SimpleJwt\Interfaces\Encrypter 接口
             */
            'drivers' => [
                Encrypter\PasswordHashEncrypter::alg() => Encrypter\PasswordHashEncrypter::class,
                Encrypter\CryptEncrypter::alg()        => Encrypter\CryptEncrypter::class,
                Encrypter\SHA1Encrypter::alg()         => Encrypter\SHA1Encrypter::class,
                Encrypter\Md5Encrypter::alg()          => Encrypter\Md5Encrypter::class,
            ],

            /*
             * 可选配置
             * 编码类
             */
            'encoder' => new Encoders\Base64UrlSafeEncoder(),
            //            'encoder' => new Encoders\Base64Encoder(),

            /*
             * 可选配置
             * 缓存类
             */
            //            'cache' => new \Doctrine\Common\Cache\FilesystemCache(sys_get_temp_dir()),
            // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
            'cache' => function () {
                return make(\Qbhy\HyperfAuth\HyperfRedisCache::class);
            },

            /*
             * 可选配置
             * 缓存前缀
             */
            'prefix' => env('SIMPLE_JWT_PREFIX', 'default'),
        ],
        'session' => [
            'driver'   => Qbhy\HyperfAuth\Guard\SessionGuard::class,
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'members' => [
            'driver' => \Qbhy\HyperfAuth\Provider\EloquentProvider::class,
            'model'  => App\Model\Member::class, //  需要实现 Qbhy\HyperfAuth\Authenticatable 接口
        ],
    ],
];
