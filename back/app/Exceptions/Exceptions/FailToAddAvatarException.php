<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class FailToAddAvatarException extends BaseException
{
    public function __construct(string $systemMessage)
    {
        parent::__construct(
            'Fail to create resource',
            [
                JsonFieldNames::MESSAGE->value => 'Failed to create avatar for the user',
                JsonFieldNames::SYSTEM_MESSAGE->value => $systemMessage,
            ],
            409
        );
    }
}
