<?php

namespace App\Enums;

enum RequestRules: string
{
case REQUIRED = 'required';
case DATE = 'Date';
case STRING = 'string';
case PROHIBITED = 'prohibited';
case ARRAY = 'array';
}
