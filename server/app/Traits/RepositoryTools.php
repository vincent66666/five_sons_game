<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Traits;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;

trait RepositoryTools
{
    /**
     * @param array $filter
     *
     * @return Builder|Model
     */
    public function queryBuilder(array $filter = [])
    {
        $instance = $this->getModel();
        if (is_array($filter) && ! empty($filter)) {
            $instance = $instance::query();
            $instance = queryFilter($filter, $instance);
        }
        return $instance;
    }

    /**
     * 通过主键id/ids获取信息.
     *
     * @param $id
     * @param bool $useCache 是否使用模型缓存
     *
     * @return array
     */
    public function findBy($id, $useCache = false): array
    {
        $instance = $this->queryBuilder();

        if ($useCache === true) {
            $modelCache = is_array($id) ? $instance->findManyFromCache($id) : $instance->findFromCache($id);
            return isset($modelCache) && $modelCache ? $modelCache->toArray() : [];
        }
        $query = $instance->find($id);

        return $query ? $query->toArray() : [];
    }

    /**
     * 通过主键id/ids指定字段获取信息.
     *
     * @param $id
     * @param array $cols
     *
     * @return array
     */
    public function findForSelect($id, $cols = ['*']): array
    {
        $instance = $this->queryBuilder();
        if (is_array($cols) && $cols[0] != '*') {
            $instance->select($cols);
        }
        $result = $instance->find($id);
        return $result ? $result->toArray() : [];
    }

    /**
     * 创建/修改记录.
     *
     * @param array $data 保存数据
     * @param bool $type 是否强制写入，适用于主键是规则生成情况
     *
     * @return array
     */
    public function saveData(array $data, $type = false): array
    {
        $id         = null;
        $instance   = $this->getModel();
        $primaryKey = $instance->getKeyName();
        if (isset($data[$primaryKey]) && $data[$primaryKey] && ! $type) {
            $id = $data[$primaryKey];
            unset($data[$primaryKey]);
            $query = $instance::query()->find($id);
            foreach ($data as $k => $v) {
                if (isset($v)) {
                    $instance->{$k} = $v;
                }
            }
            $query->save();
            return $query ? $query->toArray() : [];
        }

        foreach ($data as $k => $v) {
            if (isset($v)) {
                $instance->{$k} = $v;
            }
        }
        $instance->save();
        return $instance ? $instance->toArray() : [];
    }

    /**
     * 更新数据表字段数据.
     *
     * @param array $filter 筛选条件
     * @param array $data 更新数据
     *
     * @return array
     */
    public function updateOneBy(array $filter, array $data): array
    {
        $instance = $this->queryBuilder($filter);
        $query    = $instance->first();
        foreach ($data as $k => $v) {
            $query->{$k} = $v;
        }
        $query->save();
        return $query ? $query->toArray() : [];
    }

    /**
     * 新增数据 不走model 修改器.
     *
     * @param array $data
     * @param bool $getId
     *
     * @return bool|int
     */
    public function insertBy(array $data, $getId = false)
    {
        $instance = $this->queryBuilder();
        if ($getId) {
            return $instance->insertGetId($data);
        }
        return $instance->insert($data);
    }

    /**
     * 走model修改器.
     *
     * @param array $data
     *
     * @return array
     */
    public function createBy(array $data): array
    {
        $instance = $this->queryBuilder();
        return $instance->create($data)->toArray();
    }

    /**
     * 更新数据.
     *
     * @param array $filter
     * @param array $data
     *
     * @return int
     */
    public function updateBy(array $filter, array $data): int
    {
        $instance = $this->queryBuilder($filter);
        return $instance->update($data);
    }

    /**
     * 根据条件获取一条结果.
     *
     * @param array $filter 查询条件
     * @param array $cols 显示的字段
     *
     * @return array
     */
    public function findOneBy(array $filter, $cols = ['*']): array
    {
        $instance = $this->queryBuilder($filter);

        if (is_array($cols) && $cols[0] != '*') {
            $instance->select($cols);
        }

        $query = $instance->first();

        return empty($query) ? [] : $query->toArray();
    }

    /**
     * 根据条件获取结果.
     *
     * @param array $filter 查询条件
     * @param array $cols 显示的字段
     * @param mixed $orderBy
     *
     * @return array
     */
    public function getAllBy(array $filter = [], $cols = ['*'], $orderBy = []): array
    {
        $instance = $this->queryBuilder($filter);
        if (is_array($cols) && $cols[0] != '*') {
            $instance->select($cols);
        }
        if ($orderBy) {
            foreach ($orderBy as $column => $direction) {
                $instance->orderBy($column, $direction);
            }
        }
        $query = $instance->get();
        return empty($query) ? [] : $query->toArray();
    }

    /**
     * @param array $filter
     * @param string[] $columns
     * @param int $perPage
     * @param array $orderBy
     *                       通用列表分页
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function getListByPage(array $filter = [], $columns = ['*'], $perPage = 10, $orderBy = []): \Hyperf\Contract\LengthAwarePaginatorInterface
    {
        $instance = $this->queryBuilder($filter);
        if ($orderBy) {
            foreach ($orderBy as $column => $direction) {
                $instance->orderBy($column, $direction);
            }
        }
        return $instance->paginate($perPage, $columns);
    }

    /**
     * 统计数量.
     *
     * @param array $filter
     *
     * @return int
     */
    public function countBy(array $filter): int
    {
        $qb = $this->model::query();
        $qb = queryFilter($filter, $qb);
        return $qb->count();
    }

    /**
     * 计算.
     *
     * @param array $filter
     * @param string $column
     *
     * @return int
     */
    public function sumBy(array $filter, string $column): int
    {
        $qb = $this->model::query();
        $qb = queryFilter($filter, $qb);
        return $qb->sum($column);
    }

    /**
     * 删除数据.
     *
     * @param array $filter
     *
     * @return int|mixed
     */
    public function deleteBy(array $filter): int
    {
        $qb = $this->model::query();
        $qb = queryFilter($filter, $qb);
        return $qb->delete();
    }

    /**
     * 最大值
     *
     * @param array $filter
     * @param string $column
     *
     * @return mixed
     */
    public function maxBy(array $filter, string $column)
    {
        $qb = $this->model::query();
        $qb = queryFilter($filter, $qb);
        return $qb->max($column);
    }
}
