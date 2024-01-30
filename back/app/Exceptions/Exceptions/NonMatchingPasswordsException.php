<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class NonMatchingPasswordsException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct(
            'Invalid authentication',
            [JsonFieldNames::PASSWORD->value => $message],
            401
        );
    }
}
