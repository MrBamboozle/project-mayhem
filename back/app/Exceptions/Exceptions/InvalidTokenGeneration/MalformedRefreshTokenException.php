<?php

namespace App\Exceptions\Exceptions\InvalidTokenGeneration;

use App\Exceptions\BaseException;

class MalformedRefreshTokenException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'Malformed refresh token',
            ['token' => 'Unable to generate access token from refresh token'],
            401
        );
    }
}
