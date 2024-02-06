<?php

namespace App\Enums;

use ValueError;

enum EventEngagementType: string
{
    case WATCH = 'watch';
    case ATTEND = 'attend';
    case DETACH = 'detach';
    case UNDEFINED = 'undefined';

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

    public function isDetach(): bool
    {
        return $this === self::DETACH;
    }
}
