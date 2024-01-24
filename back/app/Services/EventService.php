<?php

namespace App\Services;

use App\Enums\JsonFieldNames;
use App\Http\Clients\NormatimOsmClient;
use App\Models\City;
use App\Models\Event;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventService
{
    public function __construct(
        private readonly NormatimOsmClient $osmClient,
    ) {}


    /**
     * @throws Exception
     */
    public function createEvent(array $data): Event
    {
        $event = Event::factory()->make([
            JsonFieldNames::TITLE->value => $data[JsonFieldNames::TITLE->value],
            JsonFieldNames::TAG_LINE->snakeCase() => $data[JsonFieldNames::TAG_LINE->value],
            JsonFieldNames::DESCRIPTION->value => $data[JsonFieldNames::DESCRIPTION->value],
            JsonFieldNames::START_TIME->snakeCase() => new DateTime($data[JsonFieldNames::START_TIME->value]),
            JsonFieldNames::END_TIME->snakeCase() => new DateTime($data[JsonFieldNames::END_TIME->value]),
            JsonFieldNames::LOCATION->value => $data[JsonFieldNames::LOCATION->value],
            JsonFieldNames::USER_ID->snakeCase() => Auth::user()->id,
        ]);

        $address = $data['address'] ?? null;

        if (empty($address)) {
            $address = $this->osmClient->getOsmAddress($data[JsonFieldNames::LOCATION->value]);
        }

        $event->address = json_encode($address);
        $city = $this->getCity($address[JsonFieldNames::CITY->value], $address[JsonFieldNames::COUNTRY_SUBDIVISION_ID->value]);
        $event->city_id = $city?->id;
        $event->save();

        return $event;
    }

    private function getCity(?string $cityName, string $subdivisionId): City|null
    {
        if (empty($cityName)) {
            return null;
        }

        $cityCollection = City::where(
            JsonFieldNames::COUNTRY_SUBDIVISION_ID->snakeCase(),
            'like',
            $subdivisionId
        )->where(
            JsonFieldNames::NAME->value,
            'like',
            '%' . $cityName . '%'
        )->get();

        if ($cityCollection->isEmpty()) {
            return City::factory()->createOne([
                JsonFieldNames::COUNTRY_SUBDIVISION_ID->snakeCase() => $subdivisionId,
                JsonFieldNames::NAME->value => $cityName,
            ]);
        }

        return $cityCollection->first();
    }
}
