<?php

namespace App\Services;

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
            'title' => $data['title'],
            'tag_line' => $data['tag_line'],
            'description' => $data['description'],
            'time' => new DateTime($data['time']),
            'location' => $data['location'],
            'user_id' => Auth::user()->id,
        ]);

        $address = $data['address'] ?? null;

        if (empty($address)) {
            $address = $this->osmClient->getOsmAddress($data['location']);
        }

        $event->address = json_encode($address);
        $city = $this->getCity($address['city'], $address['countrySubdivisionId']);
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
            'country_subdivision_id',
            'like',
            $subdivisionId
        )->where(
            'name',
            'like',
            '%' . $cityName . '%'
        )->get();

        if ($cityCollection->isEmpty()) {
            return City::factory()->createOne([
                'country_subdivision_id' => $subdivisionId,
                'name' => $cityName,
            ]);
        }

        return $cityCollection->first();
    }
}
