<?php

namespace App\Enums;

enum Route: string
{
    case LOGIN = 'login';

    case REGISTER = 'register';

    case CITIES = 'cities';

    case CATEGORIES = 'categories';

    case LOGOUT = 'logout';

    case ME = 'me';

    case USERS = 'users';

    case AVATARS = 'avatars';

    case LOCATION = 'location';

    case EVENTS = 'events';

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

    public function hasConfig(): bool
    {
        return !empty($this->config());
    }

    public function hasFilterConfig(): bool
    {
        return !empty($this->filterConfig());
    }


    public function config(): array|null
    {
        return config("url_query_fields.$this->value");
    }

    public function filterConfig(): array|null
    {
        return $this->config()['filters'] ?? null;
    }

    public function allowAllFilter(): bool
    {
        return $this->config()['allowAll'] ?? false;
    }

    public function sortConfig(): array|null {
        return $this->config()['sorts'] ?? null;
    }

    public function hasSortConfig(): bool
    {
        return !empty($this->sortConfig());
    }
}
