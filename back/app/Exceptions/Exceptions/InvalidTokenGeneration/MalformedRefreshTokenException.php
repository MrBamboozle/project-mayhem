<?php

namespace App\Exceptions\Exceptions\InvalidTokenGeneration;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class MalformedRefreshTokenException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'Malformed refresh token',
            [JsonFieldNames::TOKEN->name => 'Unable to generate access token from refresh token'],
            403
        );
    }
}
