<?php

namespace App\Enums;

enum Operators: string
{
    case LIKE = 'like';
    case AND = 'and';
    case ASCENDING = 'asc';
    case DESCENDING = 'desc';
    case UNDEFINED = 'undefined';

    public static function create(string $value): self
    {
        try {
            return self::from($value);
        } catch (\ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isValidSortOperator(): bool
    {
        return match ($this) {
            self::ASCENDING, self::DESCENDING => true,
            default => false
        };
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }
}
