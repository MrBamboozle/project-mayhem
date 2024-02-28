<?php

namespace App\Enums\Filters;

use ValueError;

enum EventFilter: string implements FilterContract
{
    case USER_ID = 'userId';

    case TITLE = 'title';

    case TAG_LINE = 'tagLine';

    case DESCRIPTION = 'description';

    case START_TIME = 'startTime';

    case START_TIME_TO = 'startTimeTo';

    case END_TIME = 'endTime';

    case END_TIME_TO = 'endTimeTo';

    case CREATOR = 'creator';

    case CITY = 'city';

    case CATEGORIES = 'categories';

    case ALL = 'all';

    case UNDEFINED = 'undefined';
//Route::EVENTS->value => [

    public static function create(string $value): self
    {
        try {
            return self::from($value);
        } catch (ValueError) {
            return self::UNDEFINED;
        }
    }

    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }

    public function filterMethod(): string
    {
        return match ($this) {
            self::USER_ID => 'filterByUserId',
            self::TITLE => 'filterByTitle',
            self::TAG_LINE => 'filterByTagLine',
            self::DESCRIPTION => 'filterByDescription',
            self::START_TIME => 'filterByStartTime',
            self::END_TIME => 'filterByEndTime',
            self::CREATOR => 'filterByCreator',
            self::CITY => 'filterByCity',
            self::CATEGORIES => 'filterByCategories',
            self::ALL => 'filterByAll',
            default => $this->value,
        };
    }
}
