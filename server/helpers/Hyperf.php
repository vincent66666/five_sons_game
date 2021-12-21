<?php

declare(strict_types=1);

/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use App\Plugins\Contracts\FileUploadInterface;
use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Contract\IdGeneratorInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Server\ServerFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Str;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as WebSocketServer;

/*
 * 容器实例
 */
if (! function_exists('container')) {
    function container(): Psr\Container\ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (! function_exists('queryFilter')) {
    /**
     * 构造查询条件.
     *
     * @param array $filter
     * @param $instance
     *
     * @return mixed
     */
    function queryFilter(array $filter, $instance): Hyperf\Database\Model\Builder
    {
        $operatorsData = [
            'eq'       => '=',
            'lt'       => '<',
            'gt'       => '>',
            'lte'      => '<=',
            'gte'      => '>=',
            'neq2'     => '<>',
            'neq'      => '!=',
            '%'        => 'like',
            'contains' => 'like',
        ];
        if (! empty($filter['with'])) {
            $instance = $instance->with($filter['with']);
            unset($filter['with']);
        }
        foreach ($filter as $field => $value) {
            $column    = $field;
            $operators = '';
            if (Str::contains($field, '|')) {
                [$column, $operators] = explode('|', $field);
            }
            if (! empty($operators)) {
                $operators = $operatorsData[$operators] ?? $operators;
            }
            if ($operators == '') {
                if (is_array($value)) {
                    $instance = $instance->whereIn($column, $value);
                } elseif ($column == 'whereRaw') {
                    $instance = $instance->whereRaw($value);
                } elseif ($column == 'whereNull') {
                    $instance = $instance->whereNull($value);
                } elseif ($column == 'whereExists') {
                    $instance = $instance->whereExists($value);
                } elseif ($column == 'whereNotNull') {
                    $instance = $instance->whereNotNull($value);
                } else {
                    $instance = $instance->where($column, $value);
                }
            } else {
                switch ($operators) {
                    case 'in':
                        $instance = $instance->whereIn($column, $value);
                        break;
                    case 'notIn':
                        $instance = $instance->whereNotIn($column, $value);
                        break;
                    case 'between':
                        $instance = $instance->whereBetween($column, $value);
                        break;
                    case 'notBetween':
                        $instance = $instance->whereNotBetween($column, $value);
                        break;
                    case 'like':
                        $instance = $instance->where($column, $operators, '%' . $value . '%');
                        break;
                    case 'notNull':
                        $instance = $instance->whereNotNull($column);
                        break;
                    case 'null':
                        $instance = $instance->whereNull($column);
                        break;
                    case 'raw':
                        $instance = $instance->whereRaw($column);
                        break;
                    case 'exists':
                        $instance = $instance->whereExists($value);
                        break;
                    default:
                        $instance = $instance->where($column, $operators, $value);
                }
            }
        }
        return $instance;
    }
}

if (! function_exists('filesystem')) {
    /**
     * @param string $adapterName
     *
     * @return \League\Flysystem\Filesystem
     */
    function filesystem(string $adapterName = 'local_public'): League\Flysystem\Filesystem
    {
        return container()->get(\Hyperf\Filesystem\FilesystemFactory::class)->get($adapterName);
    }
}

if (! function_exists('filesystemAdapter')) {
    function filesystemAdapter(string $adapterName = 'local_public')
    {
        return container()->get(\Hyperf\Filesystem\FilesystemFactory::class)->getAdapter(config('file'), $adapterName);
    }
}

if (! function_exists('fileUpload')) {
    /**
     * @return FileUploadInterface
     */
    function fileUpload(): FileUploadInterface
    {
        return container()->get(FileUploadInterface::class);
    }
}

if (! function_exists('jwt')) {
    /**
     * @param string $guard
     *
     * @return \Qbhy\SimpleJwt\JWTManager
     */
    function jwt(string $guard = 'admin'): Qbhy\SimpleJwt\JWTManager
    {
        return auth()->guard($guard)->getJwtManager();
    }
}

/*
 * redis
 */
if (! function_exists('redis')) {
    function redis(): Hyperf\Redis\Redis
    {
        return container()->get(\Hyperf\Redis\Redis::class);
    }
}

/*
 * producer AMQP投递者实例
 */
if (! function_exists('producer')) {
    function producer(): Producer
    {
        return make(Producer::class);
    }
}
/*
 * producer guzzle 实例
 */
if (! function_exists('guzzle')) {
    function guzzle(): ClientFactory
    {
        return make(ClientFactory::class);
    }
}

if (! function_exists('server')) {
    function server()
    {
        return make(ServerFactory::class)->getServer()->getServer();
    }
}

if (! function_exists('websocket')) {
    function websocket(): WebSocketServer
    {
        return make(WebSocketServer::class);
    }
}

if (! function_exists('socketIo')) {
    function socketIo(): Hyperf\SocketIOServer\SocketIO
    {
        return container()->get(Hyperf\SocketIOServer\SocketIO::class);
    }
}

/*
 * 缓存实例 简单的缓存
 */
if (! function_exists('cache')) {
    function cache(): Psr\SimpleCache\CacheInterface
    {
        return make(Psr\SimpleCache\CacheInterface::class);
    }
}

/*
 * 控制台日志
 */
if (! function_exists('stdLog')) {
    function stdLog(): StdoutLoggerInterface
    {
        return make(StdoutLoggerInterface::class);
    }
}

/*
 * 文件日志
 */
if (! function_exists('logger')) {
    function logger($name = 'hyperf', $group = 'default'): Psr\Log\LoggerInterface
    {
        return make(LoggerFactory::class)->make($name, $group);
    }
}

if (! function_exists('generator')) {
    function generator(): IdGeneratorInterface
    {
        return make(IdGeneratorInterface::class);
    }
}

if (! function_exists('request')) {
    /**
     * 请求方法辅助方法.
     *
     * @return mixed|RequestInterface
     */
    function request(): RequestInterface
    {
        return make(RequestInterface::class);
    }
}

if (! function_exists('server_request')) {
    /**
     * 请求方法辅助方法.
     */
    function server_request(): ServerRequestInterface
    {
        return make(ServerRequestInterface::class);
    }
}

if (! function_exists('response')) {
    /**
     * 响应方法辅助方法.
     *
     * @return mixed|ResponseInterface
     */
    function response(): ResponseInterface
    {
        return make(ResponseInterface::class);
    }
}

if (! function_exists('validator')) {
    function validator(
        array $data,
        array $rules,
        array $customAttributes = [],
        array $messages = []
    ): Hyperf\Contract\ValidatorInterface {
        return make(ValidatorFactoryInterface::class)
            ->make($data, $rules, $messages, $customAttributes);
    }
}

if (! function_exists('getCoId')) {
    /**
     * getCoId
     * 获取当前协程id.
     */
    function getCoId(): int
    {
        return Coroutine::id();
    }
}

if (! function_exists('getClientInfo')) {
    /**
     * getClientInfo
     * 获取请求客户端信息，获取连接的信息.
     *
     * @return mixed
     */
    function getClientInfo()
    {
        // 得从协程上下文取出请求
        $request = Context::get(ServerRequestInterface::class);
        $server  = make(SwooleServer::class);
        return $server->getClientInfo($request->getSwooleRequest()->fd);
    }
}

if (! function_exists('flushCache')) {
    /**
     * @param string $prefix
     * @param array $data
     *                    清理 Cacheable 生成的缓存
     *
     * @return bool
     */
    function flushCache(string $prefix = '', array $data = []): bool
    {
        $dispatcher = container()->get(\Psr\EventDispatcher\EventDispatcherInterface::class);
        $dispatcher->dispatch(new \Hyperf\Cache\Listener\DeleteListenerEvent($prefix, $data));
        return true;
    }
}

if (! function_exists('eventDispatcher')) {
    function eventDispatcher(): Psr\EventDispatcher\EventDispatcherInterface
    {
        return container()->get(\Psr\EventDispatcher\EventDispatcherInterface::class);
    }
}
