<?php

namespace App\Enums\Filters;

interface FilterContract
{
    public static function create(string $value): self;

    public function filterMethod(): string;

    public function isUndefined(): bool;
}
