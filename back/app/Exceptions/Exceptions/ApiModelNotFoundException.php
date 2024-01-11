<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class ApiModelNotFoundException extends BaseException
{
    public function __construct(string $id, string $model)
    {
        parent::__construct(
            "$model model not found",
            [JsonFieldNames::ID->value => "No $model model with id: $id"],
            401
        );
    }
}
