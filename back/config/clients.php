<?php

use App\Http\Clients\NormatimOsmClient;

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

    NormatimOsmClient::class => [
        'url' => 'https://nominatim.openstreetmap.org/',
        'format' => 'format=json',
        'language' => 'en',
    ]
];
