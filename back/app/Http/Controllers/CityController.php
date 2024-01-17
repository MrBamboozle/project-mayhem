<?php

namespace App\Http\Controllers;

use App\Models\City;

class CityController extends Controller
{
    public function __invoke(): array
    {
        return City::all()->toArray();
    }
}
