<?php

namespace App\Services\UrlQuery\UrlQueries;

use App\Enums\Operators;

class UrlFilter
{

    /**
     * @param string $fieldName
     * @param Operators $operator
     * @param string $fieldValue
     * @param bool $orWhere
     */
    public function __construct(
        public readonly string $fieldName,
        public readonly Operators $operator,
        public readonly string $fieldValue,
        public readonly bool $orWhere,
    ){}
}
