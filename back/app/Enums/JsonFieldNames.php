<?php

namespace App\Enums;

use Illuminate\Support\Str;
use ValueError;

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
    case TITLE = 'title';
    case TAG_LINE = 'tagLine';
    case DESCRIPTION = 'description';
    case START_TIME = 'startTime';
    case END_TIME = 'endTime';
    case USER_ID = 'userId';
    case COUNTRY_SUBDIVISION_ID = 'countrySubdivisionId';
    case CITY = 'city';

    case CITY_ID = 'cityId';

    case CATEGORIES = 'categories';

    case ADDRESS = 'address';

    case EVENT_ID = 'eventId';

    case ENGAGEMENT_TYPE = 'engagementType';

    case UNDEFINED = 'undefined';

    public static function create(string $value): self
    {
        try {
            return self::from($value);
        } catch (ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isUser(): bool
    {
        return $this === self::USER;
    }

    public function isUserId(): bool
    {
        return $this === self::USER_ID;
    }

    public function isCity(): bool
    {
        return $this === self::CITY;
    }

    public function isCityId(): bool
    {
        return $this === self::CITY_ID;
    }

    public function snakeCase(): string
    {
        return Str::snake($this->value);
    }
}
