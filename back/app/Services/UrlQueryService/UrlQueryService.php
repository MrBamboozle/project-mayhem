<?php

namespace App\Services\UrlQueryService;

use App\Enums\Operators;
use App\Enums\Route;

class UrlQueryService
{
    public function createUrlFilters(?array $incomingFilters, Route $routeEnum): array|null
    {
        if (empty($incomingFilters) || !$routeEnum->hasFilterConfig()) {
            return null;
        }

        $filters = [];

        foreach ($routeEnum->filterConfig() as $fieldName => $operator) {
            $value = $incomingFilters[$fieldName] ?? null;

            if (key_exists('all', $incomingFilters) && $routeEnum->allowAllFilter()) {
                $value = $incomingFilters['all'];
            }

            if (empty($value)) {
                continue;
            }

            $filters[] = new UrlFilter(
                $fieldName,
                $operator,
                $value,
                $routeEnum->orWhereConfig(),
            );
        }

        return $filters;
    }

    public function createUrlSorts(?array $incomingSorts, Route $routeEnum): array|null
    {
        if (empty($incomingSorts) || !$routeEnum->hasSortConfig()) {
            return null;
        }

        $sorts = [];

        foreach ($routeEnum->sortConfig() as $fieldName) {
            $incomingValue = $incomingSorts[$fieldName] ?? '';
            $value = Operators::create($incomingValue);

            if (!$value->isValidSortOperator()) {
                continue;
            }

            $sorts[] = new UrlSort(
                $fieldName,
                $value,
            );
        }

        return $sorts;
    }
}
