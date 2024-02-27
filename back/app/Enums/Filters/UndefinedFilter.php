<?php

namespace App\Enums\Filters;

use ValueError;

enum UndefinedFilter: string implements FilterContract
{
    case UNDEFINED = 'undefined';

    public static function create(string $value): self
    {
        return self::UNDEFINED;
    }

    public function isUndefined(): bool
    {
        return true;
    }

    public function filterMethod(): string
    {
        return 'none';
    }
}
