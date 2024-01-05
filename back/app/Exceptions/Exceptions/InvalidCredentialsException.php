<?php

namespace App\Exceptions\Exceptions;

use App\Exceptions\BaseException;

class InvalidCredentialsException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid login attempt',
            ['credentials' => 'Email or password are invalid'],
            401
        );
    }
}
