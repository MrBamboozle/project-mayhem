<?php

namespace App\Enums;

enum QueryField: string
{
    case FILTER = 'filter';
    case SORT = 'sort';
    case PER_PAGE = 'perPage';
    case UNDEFINED = 'undefined';
}
