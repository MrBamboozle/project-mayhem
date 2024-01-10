<?php

namespace App\Interfaces;

use BackedEnum;

interface ModelFields extends BackedEnum
{
    public static function create(string $value): self;

    public function isUndefined(): bool;

    public function isAll(): bool;
}
