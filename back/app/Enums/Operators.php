<?php

namespace App\Enums;

enum Operators: string
{
    case LIKE = 'like';
    case AND = 'and';
    case ASCENDING = 'asc';
    case DESCENDING = 'desc';
    case UNDEFINED = 'undefined';

    public static function getDirection(string $direction): self
    {
        return match ($direction) {
            self::ASCENDING->value => self::ASCENDING,
            self::DESCENDING->value => self::DESCENDING,
            default => self::UNDEFINED
        };
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }
}
