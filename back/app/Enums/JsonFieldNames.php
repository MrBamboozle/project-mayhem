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

    case REPEAT_PASSWORD = 'repeatPassword';

    case NAME = 'name';

    case EMAIL = 'email';

    case CREDENTIALS = 'credentials';

    case SYSTEM_MESSAGE = 'systemMessage';

    public function snakeCase(): string
    {
        return Str::snake($this->value);
    }
}
