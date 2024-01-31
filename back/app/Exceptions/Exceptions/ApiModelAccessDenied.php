<?php

namespace App\Exceptions\Exceptions;

use App\Enums\JsonFieldNames;
use App\Exceptions\BaseException;

class ApiModelAccessDenied extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            "Access denied",
            [JsonFieldNames::SYSTEM_MESSAGE->value => "You can not perform this action"],
            403
        );
    }
}
