<?php

use App\Enums\Operators;
use App\Enums\Route;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'applyToAllKey' => 'all',

    Route::USERS->value => [
        'filters' => [
            'name' => Operators::LIKE,
            'email' => Operators::LIKE,
        ],
        'sorts' => [
            'name',
            'email',
        ],
        'orWhere' => true,
        'allowAll' => true,
    ],

    Event::class => [
        'filters' => [
            'name',
            'email',
        ],
        'sorts' => [
            'name',
            'email',
        ],
    ],
];
