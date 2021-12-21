<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Controller;

use App\Constants\ErrorCode;
use App\Exception\ErrorResponseException;
use App\Repository\Repository;
use App\Traits\ModuleInstance;
use Hyperf\DbConnection\Model\Model;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    use ModuleInstance;

    /**
     * @var string
     */
    public string $connection = 'default';

    /**
     * __get
     * 隐式注入服务类
     * User：YM
     * Date：2019/11/21
     * Time：上午9:27.
     *
     * @param $key
     *
     * @return ContainerInterface|Model|Repository|void
     */
    public function __get($key)
    {
        if ($key == 'app') {
            return container();
        }
        $suffix = strstr($key, 'Repo');
        if ($suffix && ($suffix == 'Repo' || $suffix == 'Repository')) {
            $repoName = $suffix == 'Repo' ? $key . 'sitory' : $key;
            return $this->getRepositoriesInstance($repoName, static::class, 15);
        }
        if (substr($key, -7) == 'Service') {
            return $this->getServiceInstance($key, static::class, 15);
        }
        if (substr($key, -5) == 'Model') {
            $key = strstr($key, 'Model', true);
            return $this->getModelInstance($key, static::class, 15);
        }
        if (substr($key, -3) == 'Mdl') {
            $key = strstr($key, 'Mdl', true);
            return $this->getModelInstance($key, static::class, 28);
        }
        throw new ErrorResponseException(ErrorCode::SERVER_ERROR, "仓库/模型/服务 {$key}不存在，书写错误！");
    }
}
