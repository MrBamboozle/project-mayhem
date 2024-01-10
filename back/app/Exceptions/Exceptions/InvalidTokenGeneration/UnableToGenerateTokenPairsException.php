<?php

namespace App\Exceptions\Exceptions\InvalidTokenGeneration;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class UnableToGenerateTokenPairsException extends BaseException
{
    public function __construct(string $message = '')
    {
        parent::__construct(
            'Unable to generate token pair',
            [
                JsonFieldNames::TOKEN->value => 'Unable to generate access token',
                JsonFieldNames::REFRESH_TOKEN->value => 'Unable to generate refresh token',
                JsonFieldNames::SYSTEM_MESSAGE->value => $message,
            ],
            500
        );
    }
}
