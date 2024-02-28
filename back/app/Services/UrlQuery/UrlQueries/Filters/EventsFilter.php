<?php

namespace App\Services\UrlQuery\UrlQueries\Filters;

use App\Enums\Filters\EventFilter;
use App\Enums\Operators;
use App\Enums\Route;
use App\Traits\ApplyFilters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EventsFilter
{
    use ApplyFilters {
        ApplyFilters::applyFilters as applyTraitFilters;
    }

    public function applyFilters(Builder $query, ?array $filters): Builder
    {

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

    private function filterByTitle(Builder $query, string $value): Builder
    {
        return $query->where('title', Operators::LIKE->value, "%$value%");
    }

    private function filterByTagLine(Builder $query, string $value): Builder
    {
        return $query->where('tag_line', Operators::LIKE->value, "%$value%");
    }

    private function filterByDescription(Builder $query, string $value): Builder
    {
        return $query->where('description', Operators::LIKE->value, "%$value%");
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

    private function filterByCategories(Builder $query, string $value): Builder
    {
        return $query->where('category_id', Operators::EQUALS->value, $value);
    }

    private function filterByAll(Builder $query, string $value): Builder
    {
        $query = $this->filterByTitle($query, $value);
        $query = $this->filterByTagLine($query, $value);

        return $this->filterByDescription($query, $value);
    }

    private function isRangeProvided(?array $filters, EventFilter $filter): bool
    {
        return array_key_exists($filter->value, $filters);
    }
}
