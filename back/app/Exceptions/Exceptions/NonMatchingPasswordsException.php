<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class NonMatchingPasswordsException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid registration',
            [JsonFieldNames::PASSWORD->value => 'Password and repeat password do not match'],
            401
        );
    }
}
