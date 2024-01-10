<?php

namespace App\Enums;

use App\Enums\ModelFields\UserFields;
use App\Interfaces\ModelFields;
use App\Models\User;
use ValueError;

enum FilterableSortableModels: string
{
    case USER = User::class;
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

    public function getFilterableFieldEnum(string $fieldName): ModelFields
    {
        return match ($this) {
            self::USER => UserFields::create($fieldName),
            default => UserFields::UNDEFINED
        };
    }

    public function getSortableFieldEnum(string $fieldName): ModelFields
    {
        return match ($this) {
            self::USER => UserFields::create($fieldName),
            default => UserFields::UNDEFINED
        };
    }
}
