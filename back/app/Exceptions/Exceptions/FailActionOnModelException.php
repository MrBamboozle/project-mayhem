<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class FailActionOnModelException extends BaseException
{
    public function __construct(string $systemMessage, string $action, string $model)
    {
        parent::__construct(
            "Fail to $action",
            [
                JsonFieldNames::MESSAGE->value => "Failed to $action $model model",
                JsonFieldNames::SYSTEM_MESSAGE->value => $systemMessage,
            ],
            409
        );
    }
}
