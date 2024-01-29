<?php

namespace App\Services;

use App\Services\UrlQuery\UrlQueries\UrlFilter;
use App\Services\UrlQuery\UrlQueries\UrlSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelService
{
    public function applyFilters(Builder $query, ?array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        /** @var UrlFilter $filter */
        foreach ($filters as $filter) {
            if (is_array($filter)) {
                foreach ($filter as $filed) {
                    $this->applyFilter($filed, $query, true);
                }

                continue;
            }

            $this->applyFilter($filter, $query);
        }

        return $query;
    }

    public function applySorts(Builder $query, ?array $sorts): Builder
    {
        if (empty($sorts)) {
            return $query;
        }

        /** @var UrlSort $sort */
        foreach ($sorts as $sort) {
            $query->orderBy($sort->fieldName, $sort->value->value);
        }

        return $query;
    }

    public function applyFilter(UrlFilter $filter, Builder &$query, bool $orWhere = false): void
    {
        $data = [
            $filter->fieldName,
            $filter->operator->value,
            $filter->fieldValue,
        ];

        if ($filter->isRelation) {
            $query->withWhereHas($filter->relationName, fn ($query) => $query->where(...$data));

            return;
        }

        if ($orWhere) {
            $query->orWhere(...$data);
        }

        $query->where(...$data);
    }
}
