<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class FailToDeleteCurrentAvatar extends BaseException
{
    public function __construct(string $systemMessage)
    {
        parent::__construct(
            'Fail to delete resource',
            [
                JsonFieldNames::MESSAGE->value => 'Failed to delete current user avatar',
                JsonFieldNames::SYSTEM_MESSAGE->value => $systemMessage,
            ],
            409
        );
    }
}
