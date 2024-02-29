<?php

namespace App\Services\UrlQuery\UrlQueries\Filters;

use App\Enums\Filters\EventFilter;
use App\Enums\Operators;
use App\Traits\ApplyFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EventsFilter
{
    use ApplyFilters {
        ApplyFilters::applyFilters as applyTraitFilters;
    }

    public function applyFilters(Builder $query, ?array $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        if ($this->isRangeProvided($filters, EventFilter::START_TIME_TO)) {
            $query = $this->filterByStartTime(
                $query,
                $filters[EventFilter::START_TIME_TO->value],
                $filters[EventFilter::START_TIME->value],
            );

            unset(
                $filters[EventFilter::START_TIME_TO->value],
                $filters[EventFilter::START_TIME->value]
            );
        }

        if ($this->isRangeProvided($filters, EventFilter::END_TIME_TO)) {
            $query = $this->filterByEndTime(
                $query,
                $filters[EventFilter::END_TIME_TO->value],
                $filters[EventFilter::END_TIME->value],
            );

            unset(
                $filters[EventFilter::END_TIME_TO->value],
                $filters[EventFilter::END_TIME->value]
            );
        }

        return $this->applyTraitFilters($query, $filters);
    }

    private function filterByUserId(Builder $query, string $value): Builder
    {
        return $query->where('user_id', Operators::EQUALS->value, $value);
    }

    private function filterByTitle(Builder $query, string $value, bool $or = false): Builder
    {
        $params = [
            'column' => 'title',
            'operator' => Operators::LIKE->value,
            'value' => "%$value%",
        ];

        if ($or) {
            return $query->orWhere(...$params);
        }

        return $query->where(...$params);
    }

    private function filterByTagLine(Builder $query, string $value, bool $or = false): Builder
    {
        $params = [
            'column' => 'tag_line',
            'operator' => Operators::LIKE->value,
            'value' => "%$value%",
        ];

        if ($or) {
            return $query->orWhere(...$params);
        }

        return $query->where(...$params);
    }

    private function filterByDescription(Builder $query, string $value, bool $or = false): Builder
    {
        $params = [
            'column' => 'description',
            'operator' => Operators::LIKE->value,
            'value' => "%$value%",
        ];

        if ($or) {
            return $query->orWhere(...$params);
        }

        return $query->where(...$params);
    }


    private function filterByStartTime(Builder $query, string $value, string $from = ''): Builder
    {
        if (empty($from)) {
            $from = now();
        }

        return $query->whereBetween('start_time', [$from, $value]);
    }

    private function filterByEndTime(Builder $query, string $value, string $from = ''): Builder
    {
        if (empty($from)) {
            $from = now();
        }

        return $query->whereBetween('end_time', [$from, $value]);
    }

    private function filterByCreator(Builder $query, string $value): Builder
    {
        return $query->where('user_id', Operators::EQUALS->value, $value);
    }

    private function filterByCity(Builder $query, string $value): Builder
    {
        return $query->where('city_id', Operators::EQUALS->value, $value);
    }

    private function filterByCategories(Builder $query, string $values): Builder
    {
        $valuesArray = explode(',', $values);
        $categoryIds = [];

        foreach ($valuesArray as $categoryId) {
            if (!Str::isUuid($categoryId)) {
                continue;
            }

            $categoryIds = $categoryId;
        }

        if (empty($categoryIds)) {
            return $query;
        }

        return $query->whereHas('categories', fn (Builder $q) => $q->whereIn('categories.id', $categoryIds));
    }

    private function filterByAll(Builder $query, string $value): Builder
    {
        $query = $this->filterByTitle($query, $value, true);
        $query = $this->filterByTagLine($query, $value, true);

        return $this->filterByDescription($query, $value, true);
    }

    private function isRangeProvided(array $filters, EventFilter $filter): bool
    {
        return array_key_exists($filter->value, $filters);
    }
}
