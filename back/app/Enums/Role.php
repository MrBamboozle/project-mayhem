<?php

namespace App\Enums;

use ValueError;

enum Role: string
{
    case GODMODE = 'GODMODE';
    case ADMIN = 'ADMIN';
    case PREMIUM = 'PREMIUM';
    case REGULAR = 'REGULAR';
    case UNDEFINED = 'UNDEFINED';

    public static function create($value): self
    {
        try {
            return self::from($value);
        } catch (ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isGodMode(): bool
    {
        return $this === self::GODMODE;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }


    public function isPremium(): bool
    {
        return $this === self::PREMIUM;
    }

    public function isRegular(): bool
    {
        return $this === self::REGULAR;
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }
}
