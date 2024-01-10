<?php

namespace App\Services;

use App\Enums\FilterableSortableModels;
use App\Enums\Operators;
use App\Interfaces\ModelFields;
use Illuminate\Database\Eloquent\Builder;

class ModelService
{
    public function applyFilters(Builder $query, Operators $operator, ?array $filters, FilterableSortableModels $model): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $field => $filter) {
            $name = $model->getFilterableFieldEnum($field);

            if ($name->isUndefined() || $operator->isUndefined()) {
                continue;
            }

            if (!$name->isAll() && count($filters) > 2) {
                $query->where($name->value, $operator->value, "%$filter%");
            }

            $cases = $name::cases();

            foreach ($cases as $case) {
                if (!$case->isAll() && !$case->isUndefined()) {
                    $query->orWhere($case->value, $operator->value, "%$filter%");
                }
            }
        }

        return $query;
    }

    public function applySorts(Builder $query, ?array $sorts, FilterableSortableModels $model): Builder
    {
        if (!empty($sorts)) {
            foreach ($sorts as $sort => $value) {
                $direction = Operators::getDirection($value);
                $name = $model->getFilterableFieldEnum($sort);

                if ($name->isUndefined() || $name->isAll() ||$direction->isUndefined()) {
                    continue;
                }

                $query->orderBy($name->value, $direction->value);
            }
        }

        return $query;
    }
}
