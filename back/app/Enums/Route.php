<?php

namespace App\Enums;

use App\Enums\Filters\EventFilter;
use App\Enums\Filters\FilterContract;
use App\Enums\Filters\UndefinedFilter;
use App\Enums\Filters\UserFilter;

enum Route: string
{
    case LOGIN = 'login';

    case REGISTER = 'register';

    case CITIES = 'cities';

    case CATEGORIES = 'categories';

    case LOGOUT = 'logout';

    case ME = 'me';

    case USERS = 'users';

    case USERS_ALL = 'users-all';

    case AVATARS = 'avatars';

    case LOCATION = 'location';

    case EVENTS = 'events';

    case EVENTS_ALL = 'events-all';

    case EVENTS_PRIVATE = 'events/private';

    case REFRESH = 'refresh-token';

    case UNDEFINED = 'undefined';

    case EVENTS_ENGAGE = 'events/engage';

    case USER_NOTIFICATIONS = 'user-notifications';

    case USER_NOTIFICATIONS_ALL = 'user-notifications/all';


    public static function create(string $value): self
    {
        try {
            return self::from(
                collect(explode('/', $value))
                    ->filter(fn (string $part) => $part !== 'api')
                    ->implode('/')
            );
        } catch (\ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isEventPrivate():bool
    {
        return $this === self::EVENTS_PRIVATE;
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }

    public function path(): string
    {
        return "/$this->value";
    }

    public function filterConfig(string $filterName): FilterContract
    {
        return match ($this) {
            self::USERS, self::USERS_ALL => UserFilter::create($filterName),
            self::EVENTS, self::EVENTS_ALL => EventFilter::create($filterName),
            default => UndefinedFilter::create($filterName),
        };
    }
}
