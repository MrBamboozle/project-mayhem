<?php

namespace App\Enums;

enum QueryField: string
{
    case FILTER = 'filter';
    case SORT = 'sort';
    case UNDEFINED = 'undefined';
}
