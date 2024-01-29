<?php

namespace App\Services\UrlQuery;

use App\Enums\Operators;
use App\Enums\Route;
use App\Services\UrlQuery\UrlQueries\UrlFilter;
use App\Services\UrlQuery\UrlQueries\UrlSort;

class UrlQueryService
{
    public function createUrlFilters(?array $incomingFilters, Route $routeEnum): array|null
    {
        if (empty($incomingFilters) || !$routeEnum->hasFilterConfig()) {
            return null;
        }

        $filters = [];

        foreach ($routeEnum->filterConfig() as $fieldName => $fieldConfig) {
            $value = $incomingFilters[$fieldName] ?? null;

            if (key_exists('all', $incomingFilters) && $routeEnum->allowAllFilter() && $fieldConfig['orWhere']) {
                $value = $incomingFilters['all'];
            }

            if (empty($value)) {
                continue;
            }

            $isRelation = $fieldConfig['isRelation'];
            /** @var Operators $operator */
            $operator = $fieldConfig['operator'];

            $data = [
                'fieldName' => $fieldConfig['key'],
                'operator' => $operator,
                'fieldValue' => $operator->isLike() ? "%$value%" : $value,
                'isRelation' => $isRelation,
                'relationName' => $isRelation ? $fieldName : null,
            ];

            if ($fieldConfig['orWhere']) {
                $filters['orWhere'][] = new UrlFilter(...$data);

                continue;
            }

            $filters[$fieldName] = new UrlFilter(...$data);
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
