<?php

namespace App\Enums;

enum AllowedParams: string
{
    case FILTER = 'filter';
    case SORT = 'sort';
    case UNDEFINED = 'undefined';
}
