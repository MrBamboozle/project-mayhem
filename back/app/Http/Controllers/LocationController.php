<?php

namespace App\Http\Controllers;

use App\Enums\JsonFieldNames;
use App\Http\Clients\NormatimOsmClient;
use App\Http\Requests\LocationRequest;

class LocationController extends Controller
{
    public function __construct(
        private readonly NormatimOsmClient $osmClient
    ) {}

    public function __invoke(LocationRequest $request)
    {
        return $this->osmClient->getOsmAddress($request->get(JsonFieldNames::LOCATION->value));
    }
}
