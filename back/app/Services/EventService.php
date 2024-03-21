<?php

namespace App\Services;

use App\Enums\EventEngagementType;
use App\Enums\JsonFieldNames;
use App\Events\EventEngagement;
use App\Events\EventUpdated;
use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Http\Clients\NormatimOsmClient;
use App\Models\City;
use App\Models\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class EventService
{
    public function __construct(
        public readonly NormatimOsmClient $osmClient,
    ) {}


    /**
     * @throws Exception
     */
    public function createEvent(array $data): Event
    {
        DB::beginTransaction();
        try {
            $event = new Event();

            $event->title = $data[JsonFieldNames::TITLE->value];
            $event->tag_line = $data[JsonFieldNames::TAG_LINE->value] ?? null;
            $event->description = $data[JsonFieldNames::DESCRIPTION->value];
            $event->start_time = new Carbon($data[JsonFieldNames::START_TIME->value]);
            $event->end_time = new Carbon($data[JsonFieldNames::END_TIME->value]);
            $event->location = $data[JsonFieldNames::LOCATION->value];
            $event->user_id = Auth::user()->id;

            $address = $data['address'] ?? null;

            if (empty($address)) {
                $address = $this->osmClient->getOsmAddress($data[JsonFieldNames::LOCATION->value]);
            }

            $event->address = json_encode($address);
            $city = $this->getCity($address[JsonFieldNames::CITY->value], $address[JsonFieldNames::COUNTRY_SUBDIVISION_ID->value]);
            $event->city_id = $city?->id;
            $event->save();
            $categories = $data[JsonFieldNames::CATEGORIES->value] ?? null;

            if (!empty($categories)) {
                $event->categories()->sync($categories);
            }
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException($error->getMessage(), 'create', Event::class);
        }

        DB::commit();

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

    /**
     * @throws FailActionOnModelException
     */
    public function updateEvent(array $data, Event $event): Event
    {
        $oldEventValues = $event->load('categories')->toArray();

        DB::beginTransaction();
        try {
            foreach ($data as $field => $value) {
                if ($event->isRelation($field)) {
                    if ($event->{$field}()::class === BelongsToMany::class) {
                        $event->{$field}()->sync($value);

                        continue;
                    }

                    $updateField = $event->{$field}->getForeignKey();
                    $event->{$updateField} = $value;

                    continue;
                }

                $event->{Str::snake($field)} = $value;
            }

            $event->save();
        } catch (Throwable $error) {
             DB::rollBack();

            throw new FailActionOnModelException($error->getMessage(), 'update', Event::class);
        }

        DB::commit();

        $event->load('categories');

        EventUpdated::dispatch($event, $oldEventValues);

        return $event->load('creator', 'city');
    }

    /**
     * @throws FailActionOnModelException
     */
    public function deleteEvent(Event $event): void
    {
        $event->categories()->sync([]);
        $event->delete();
    }

    /**
     * @throws FailActionOnModelException
     */
    public function updateEventEngagement(Event $event, array $data): Event
    {
        $user = Auth::user();
        $fieldName = JsonFieldNames::ENGAGEMENT_TYPE;
        $fieldValue = $data[$fieldName->value] ?? '';
        $engagementType = EventEngagementType::create($fieldValue);

        if ($engagementType->isUndefined()) {
            throw new FailActionOnModelException(
                "Invalid engagement $fieldValue received",
                'attach engagement',
                Event::class
            );
        }

        if ($engagementType->isDetach()) {
            $event->engagingUsers()->detach($user->id);

            EventEngagement::dispatch($event, $engagementType);

            return $event;
        }

        $engagingUsers = $event->engagingUsers()->find($user->id);

        if (empty($engagingUsers)) {
            $event->engagingUsers()
                ->withPivotValue($fieldName->snakeCase(), $fieldValue)
                ->attach($user->id);

            EventEngagement::dispatch($event, $engagementType);

            return $event;
        }

        $event->engagingUsers()->updateExistingPivot(
            $user->id,
            [$fieldName->snakeCase() => $fieldValue]
        );

        EventEngagement::dispatch($event, $engagementType);

        return $event;
    }
}
