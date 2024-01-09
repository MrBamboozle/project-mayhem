<?php

namespace App\Enums;

enum TokenAbility: string
{
    case ISSUE_ACCESS_TOKEN = 'issue_access_token';
    case ACCESS_API = 'access_api';
}
