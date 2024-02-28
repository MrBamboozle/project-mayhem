<?php

namespace App\Services\UrlQuery\UrlQueries\Filters;

use App\Enums\Operators;
use App\Traits\ApplyFilters;
use Auth;
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
        $user = Auth::user()->role->enum();

        if (!$user->isGodMode() && !$user->isAdmin()) {
            return $query;
        }

        return $query->orWhere('email', Operators::LIKE->value, "%$value%");
    }

    public function filterByAll(Builder $query, string $value): Builder
    {
        $query = $this->filterByName($query, $value);

        return $this->filterByEmail($query, $value);
    }
}
