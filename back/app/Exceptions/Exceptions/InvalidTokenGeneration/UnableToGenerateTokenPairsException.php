<?php

namespace App\Exceptions\Exceptions\InvalidTokenGeneration;

use App\Exceptions\BaseException;

class UnableToGenerateTokenPairsException extends BaseException
{
    public function __construct(string $message = '')
    {
        parent::__construct(
            'Unable to generate token pair',
            [
                'access_token' => 'Unable to generate access token',
                'refresh_token' => 'Unable to generate refresh token',
                'system_message' => $message,
            ],
            500
        );
    }
}
