<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Traits;

use App\Constants\ErrorCode;
use App\Exception\ErrorResponseException;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\Str;

trait ModuleInstance
{
    /**
     * getRepositoriesInstance
     * 获取仓库类实例.
     *
     * @param $key
     * @param $classname
     * @param int $length
     *
     * @return mixed
     */
    public function getRepositoriesInstance($key, $classname, int $length = 15)
    {
        $key    = ucfirst($key);
        $key    = Str::replaceFirst('Repository', 'Interface', $key);
        $module = getModuleName($classname, $length) ?? '';
        if ($module) {
            $filename  = BASE_PATH . "/app/Repository/Interfaces/{$module}/{$key}.php";
            $classname = "App\\Repository\\Interfaces\\{$module}\\{$key}";
        } else {
            $filename  = BASE_PATH . "/app/Repository/Interfaces/{$key}.php";
            $classname = "App\\Repository\\Interfaces\\{$key}";
        }
        if (file_exists($filename)) {
            return container()->get($classname);
        }
        throw new ErrorResponseException(ErrorCode::SERVER_ERROR, "仓库{$key}不存在，书写错误！");
    }

    /**
     * 获取service类实例.
     * @param $key
     * @param $classname
     * @param int $length
     *
     * @return mixed
     */
    public function getServiceInstance($key, $classname, int $length = 15)
    {
        $key    = ucfirst($key);
        $module = getModuleName($classname, $length) ?? '';
        if ($module) {
            $filename  = BASE_PATH . "/app/Service/{$module}/{$key}.php";
            $classname = "App\\Service\\{$module}\\{$key}";
        } else {
            $filename  = BASE_PATH . "/app/Service/{$key}.php";
            $classname = "App\\Service\\{$key}";
        }
        if (file_exists($filename)) {
            return container()->get($classname);
        }
        $fileName  = BASE_PATH . "/app/Model/{$key}.php";
        $className = "App\\Model\\{$key}";
        if (file_exists($fileName)) {
            return container()->get($className);
        }
        throw new ErrorResponseException(ErrorCode::SERVER_ERROR, "服务{$key}不存在，书写错误！");
    }

    /**
     * getModelInstance
     * 获取数据模型类实例.
     *
     * @param $key
     *
     * @param $classname
     * @param int $length
     *
     * @return Model
     */
    public function getModelInstance($key, $classname, int $length = 15): Model
    {
        $key    = ucfirst($key);

        $module = getModuleName($classname, $length) ?? '';
        if ($module) {
            $fileName  = BASE_PATH . "/app/Model/{$module}/{$key}.php";
            $className = "App\\Model\\{$module}\\{$key}";
        } else {
            $fileName  = BASE_PATH . "/app/Model/{$key}.php";
            $className = "App\\Model\\{$key}";
        }
        if (file_exists($fileName)) {
            return container()->get($className);
        }

        $fileName  = BASE_PATH . "/app/Model/{$key}.php";
        $className = "App\\Model\\{$key}";
        if (file_exists($fileName)) {
            return container()->get($className);
        }
        throw new ErrorResponseException(ErrorCode::SERVER_ERROR, "模型{$key}不存在，书写错误！");
    }
}
