<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class CannotImplementThisMethodException extends BaseException
{
    public function __construct(string $method, string $class)
    {
        parent::__construct(
            "Cannot implement $method",
            [JsonFieldNames::SYSTEM_MESSAGE->value => "$class cannot implement method $method"],
            500
        );
    }
}
