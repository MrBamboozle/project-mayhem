<?php

namespace App\Services\UrlQuery\UrlQueries\Sorts;

use App\Enums\Operators;
use App\Services\UrlQuery\UrlQueries\UrlSort;
use Illuminate\Database\Eloquent\Builder;

class BaseSort
{
    public function applySorts(Builder $query, ?array $sorts): Builder
    {
        if (empty($sorts)) {
            return $query;
        }

        /** @var UrlSort $sort */
        foreach ($sorts as $sortName => $sortValue) {
            $operator = Operators::create($sortValue);

            if (!$operator->isValidSortOperator()) {
                continue;
            }

            $query->orderBy($sortName, $operator->value);
        }

        return $query;
    }
}
