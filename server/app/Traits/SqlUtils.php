<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Traits;

trait SqlUtils
{
    /**
     * @param array $multipleData
     * @param string $tableName
     * @param string $primaryKey
     * @param string $msg
     *
     * @return array|false
     */
    public function batchUpdate(
        $multipleData = [],
        $tableName = '',
        $primaryKey = 'id',
        &$msg = ''
    ) {
        try {
            if (empty($multipleData)) {
                $msg = '数据不能为空';
                return false;
            }
            $firstRow     = current($multipleData);
            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow[$primaryKey]) ? $primaryKey : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = 'UPDATE ' . $tableName . ' SET ';
            $sets      = [];
            $bindings  = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = '`' . $uColumn . '` = CASE ';
                foreach ($multipleData as $data) {
                    $setSql     .= 'WHEN `' . $referenceColumn . '` = ? THEN ? ';
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= 'ELSE `' . $uColumn . '` END ';
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn   = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings  = array_merge($bindings, $whereIn);
            $whereIn   = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ', ') . ' WHERE `' . $referenceColumn . '` IN ('
                . $whereIn . ')';
            // 传入预处理sql语句和对应绑定数据
            return [
                'query'     => $updateSql,
                '$bindings' => $bindings,
            ];
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            return false;
        }
    }
}
