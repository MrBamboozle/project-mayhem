<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class InvalidCredentialsException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid login attempt',
            [JsonFieldNames::CREDENTIALS->value => 'Email or password are invalid'],
            401
        );
    }
}
