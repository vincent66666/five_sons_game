<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Traits;

trait HasCompositePrimaryKey
{
    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            if ($this->{$key}) {
                $query->where($key, '=', $this->{$key});
            } else {
                throw new \RuntimeException(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }
        }

        return $query;
    }
}
