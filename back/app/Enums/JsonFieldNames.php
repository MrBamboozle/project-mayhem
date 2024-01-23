<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum JsonFieldNames: string
{
    case MESSAGE = 'message';
    case USER = 'user';
    case TOKEN = 'token';
    case REFRESH_TOKEN = 'refreshToken';
    case ERRORS = 'errors';
    case PASSWORD = 'password';
    case NAME = 'name';
    case EMAIL = 'email';
    case CREDENTIALS = 'credentials';
    case SYSTEM_MESSAGE = 'systemMessage';
    case ID = 'id';
    case LOCATION = 'location';

    public function snakeCase(): string
    {
        return Str::snake($this->value);
    }
}
