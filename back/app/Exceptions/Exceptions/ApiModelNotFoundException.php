<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class ApiModelNotFoundException extends BaseException
{
    public function __construct(array $ids, string $model)
    {
        $idsNotFound = implode( ', ', $ids);

        parent::__construct(
            "$model model not found",
            [JsonFieldNames::ID->value => "No $model model with ids: $idsNotFound"],
            401
        );
    }
}
