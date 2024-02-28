<?php

namespace App\Enums\Filters;

use ValueError;

enum UserFilter: string implements FilterContract
{
    case NAME = 'name';

    case EMAIL = 'email';

    case ALL = 'all';

    case UNDEFINED = 'undefined';
//Route::USERS->value

    public static function create(string $value): self
    {
        try {
            return self::from($value);
        } catch (ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }

    public function filterMethod(): string
    {
        return match ($this) {
            self::NAME => 'filterByName',
            self::EMAIL => 'filterByEmail',
            self::ALL => 'filterByAll',
            default => $this->value,
        };
    }
}
