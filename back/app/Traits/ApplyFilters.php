<?php

namespace App\Traits;

use App\Enums\Route;
use Illuminate\Database\Eloquent\Builder;

trait ApplyFilters
{
    public function applyFilters(Builder $query, ?array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $filterName => $filterValue) {
            $routeEnum = Route::create(request()->path());
            $filterEnum = $routeEnum->filterConfig($filterName);

            if ($filterEnum->isUndefined()) {
                continue;
            }

            $query = $this->{$filterEnum->filterMethod()}($query, $filterValue);
        }

        return $query;
    }
}
