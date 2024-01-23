<?php

namespace App\Services\UrlQuery\UrlQueries;

use App\Enums\Operators;

class UrlSort
{
    /**
     * @param string $fieldName
     * @param Operators $value
     */
    public function __construct(
        public readonly string $fieldName,
        public readonly Operators $value,
    ) {}
}
