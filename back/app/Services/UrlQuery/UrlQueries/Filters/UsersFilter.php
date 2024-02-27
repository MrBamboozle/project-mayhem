<?php

namespace App\Services\UrlQuery\UrlQueries\Filters;

use App\Enums\Operators;
use App\Traits\ApplyFilters;
use Illuminate\Database\Eloquent\Builder;

class UsersFilter
{
    use ApplyFilters;

    public function filterByName(Builder $query, string $value): Builder
    {
        return $query->orWhere('name', Operators::LIKE->value, "%$value%");
    }

    public function filterByEmail(Builder $query, string $value): Builder
    {
        return $query->orWhere('email', Operators::LIKE->value, "%$value%");
    }
}
