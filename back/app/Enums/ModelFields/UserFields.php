<?php

namespace App\Enums\ModelFields;

use App\Interfaces\ModelFields;
use ValueError;

enum UserFields: string implements ModelFields
{
    case EMAIL = 'email';
    case NAME = 'name';
    case ALL = 'all';
    case UNDEFINED = 'undefined';

    public static function create(string $value): self
    {
        try {
            $enum = self::from($value);
        } catch (ValueError $error) {
            $enum = self::UNDEFINED;
        }

        return $enum;
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }

    public function isAll(): bool
    {
        return $this === self::ALL;
    }
}
