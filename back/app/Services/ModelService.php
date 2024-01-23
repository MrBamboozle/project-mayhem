<?php

namespace App\Services;

use App\Services\UrlQuery\UrlQueries\UrlFilter;
use App\Services\UrlQuery\UrlQueries\UrlSort;
use Illuminate\Database\Eloquent\Builder;

class ModelService
{
    public function applyFilters(Builder $query, ?array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        /** @var UrlFilter $filter */
        foreach ($filters as $filter) {
            if ($filter->operator->isUndefined()) {
                continue;
            }

            if ($filter->orWhere) {
                $query->orWhere($filter->fieldName, $filter->operator->value, "%$filter->fieldValue%");
            } else {
                $query->where($filter->fieldName, $filter->operator->value, "%$filter->fieldValue%");
            }
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
}
