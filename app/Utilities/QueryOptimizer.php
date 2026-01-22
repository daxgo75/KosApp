<?php

namespace App\Utilities;

use Illuminate\Database\Eloquent\Builder;

class QueryOptimizer
{
    public static function eagerLoad(Builder $query, array $relations): Builder
    {
        return $query->with($relations);
    }

    public static function selectOnlyNeeded(Builder $query, array $columns): Builder
    {
        return $query->select($columns);
    }

    public static function chunk(Builder $query, int $chunkSize, callable $callback): void
    {
        $query->chunk($chunkSize, $callback);
    }

    public static function lazy(Builder $query, int $chunkSize = 1000)
    {
        return $query->lazy($chunkSize);
    }

    public static function addWhenCondition(Builder $query, bool $condition, callable $callback): Builder
    {
        return $condition ? $callback($query) : $query;
    }

    public static function countWithoutPagination(Builder $query): int
    {
        return $query->toBase()->getCountForPagination();
    }

    public static function pluckWithKey(Builder $query, string $valueColumn, string $keyColumn = 'id')
    {
        return $query->pluck($valueColumn, $keyColumn);
    }
}
