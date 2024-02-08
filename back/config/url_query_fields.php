<?php

use App\Enums\Operators;
use App\Enums\Route;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'applyToAllKey' => 'all',

    Route::USERS->value => [
        'filters' => [
            'name' => [
                'key' => 'name',
                'operator' => Operators::LIKE,
                'orWhere' => true,
                'isRelation' => false,
            ],
            'email' => [
                'key' => 'email',
                'operator' => Operators::LIKE,
                'orWhere' => false,
                'isRelation' => false,
            ],
        ],
        'sorts' => [
            'name',
            'email',
        ],
        'allowAll' => true,
    ],

    Route::EVENTS->value => [
        'filters' => [
            'userId' => [
              'key' => 'user_id',
              'operator' => Operators::EQUALS,
              'orWhere' => false,
              'isRelation' => false,
            ],
            'title' => [
                'key' => 'title',
                'operator' => Operators::LIKE,
                'orWhere' => true,
                'isRelation' => false,
            ],
            'tagLine' => [
                'key' => 'tag_line',
                'operator' => Operators::LIKE,
                'orWhere' => true,
                'isRelation' => false,
            ],
            'description' => [
                'key' => 'description',
                'operator' => Operators::LIKE,
                'orWhere' => true,
                'isRelation' => false,
            ],
            'startTime' => [
                'key' => 'start_time',
                'operator' => Operators::LARGER,
                'orWhere' => false,
                'isRelation' => false,
            ],
            'creator' => [
                'key' => 'name',
                'operator' => Operators::LIKE,
                'orWhere' => false,
                'isRelation' => true,
            ],
            'city' => [
                'key' => 'id',
                'operator' => Operators::EQUALS,
                'orWhere' => false,
                'isRelation' => true,
            ],
            'categories' => [
                'key' => 'id',
                'operator' => Operators::EQUALS,
                'orWhere' => false,
                'isRelation' => true,
            ],
        ],
        'sorts' => [
            'title',
            'tag_line',
            'description',
            'start_time',
            'user',
            'city',
        ],
        'allowAll' => false,
    ],

    /* Structure

    Route::ENUM->value => [
        'filters' => [
            'filterName' => [
                'key' => 'DB_field_name'
                'operator' => Operators::ENUM,
                'orWhere' => bool,
            ],
        ],
        'sorts' => [
            'sortName',
        ],
        'allowAll' => bool,
    ],

    */
];
