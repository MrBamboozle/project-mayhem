<?php

namespace App\Enums;

enum Operators: string
{
    case LIKE = 'like';
    case AND = 'and';
    case ASCENDING = 'asc';
    case DESCENDING = 'desc';
    case UNDEFINED = 'undefined';
    case EQUALS = '=';

    case LARGER = '>';

    public static function create(string $value): self
    {
        try {
            return self::from($value);
        } catch (\ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isLike(): bool
    {
        return $this === self::LIKE;
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
