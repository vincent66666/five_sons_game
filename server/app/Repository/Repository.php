<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Repository;

use App\Constants\ErrorCode;
use App\Exception\ErrorResponseException;
use App\Traits\ModuleInstance;
use App\Traits\RepositoryTools;
use App\Traits\SqlUtils;
use Hyperf\DbConnection\Model\Model;

/**
 * 仓库基类
 * Class Repository.
 */
class Repository
{
    use RepositoryTools;
    use SqlUtils;
    use ModuleInstance;

    /**
     * @var string
     */
    public string $connection = 'default';

    /**
     * @var string
     */
    public string $table = '';

    /**
     * @var Model
     */
    public Model $model;

    public function __construct()
    {
        if ($this->table) {
            $this->getModel();
        }
    }

    /**
     * __get
     * 隐式注入服务类
     * User：YM
     * Date：2019/11/21
     * Time：上午9:27.
     *
     * @param $key
     *
     * @return Model|\Psr\Container\ContainerInterface|void
     */
    public function __get($key)
    {
        if ($key == 'app') {
            return container();
        }
        if (substr($key, -5) == 'Model') {
            $key = strstr($key, 'Model', true);
            return $this->getModelInstance($key, static::class, 28)->setConnection($this->connection);
        }
        if (substr($key, -3) == 'Mdl') {
            $key = strstr($key, 'Mdl', true);
            return $this->getModelInstance($key, static::class, 28)->setConnection($this->connection);
        }
        throw new ErrorResponseException(ErrorCode::SERVER_ERROR, "模型{$key}不存在，书写错误！");
    }

    /**
     * 不存在方法时的处理  适用于模型创建.
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return container()->get($this->table)->setConnection($this->connection)->getModel($this->table)->{$method}(...$parameters);
    }

    /**
     * 自定义链接.
     *
     * @param string $connection
     *
     * @return Repository
     */
    public function setConnection($connection = 'default'): Repository
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * 自定义表.
     *
     * @param $table
     *
     * @return Repository
     */
    public function setTable($table): Repository
    {
        $this->table = $table;
        return $this;
    }

    /**
     * 自定义模型仓库.
     *
     * @param $table
     *
     * @return Repository
     */
    public function setModel($table = null): Repository
    {
        $this->model = container()->get($table ?? $this->table)->setConnection($this->connection);
        return $this;
    }

    /**
     * 获取模型仓库.
     *
     * @return Model
     */
    public function getModel(): Model
    {
        $this->model = container()->get($this->table)->setConnection($this->connection);
        return $this->model;
    }
}
