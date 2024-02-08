<?php

namespace App\Services;

use App\Enums\EventEngagementType;
use App\Enums\JsonFieldNames;
use App\Events\EventUpdated;
use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Http\Clients\NormatimOsmClient;
use App\Models\City;
use App\Models\Event;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

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
        DB::beginTransaction();
        try {
            $event = Event::factory()->make([
                JsonFieldNames::TITLE->value => $data[JsonFieldNames::TITLE->value],
                JsonFieldNames::TAG_LINE->snakeCase() => $data[JsonFieldNames::TAG_LINE->value] ?? null,
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
        DB::beginTransaction();
        try {
            $event->categories()->sync([]);
            $event->delete();
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException($error->getMessage(), 'delete', Event::class);
        }
        DB::commit();
    }

    /**
     * @throws FailActionOnModelException
     */
    public function updateEventEngagement(Event $event, array $data): Event
    {
        $user = Auth::user();
        $fieldName = JsonFieldNames::ENGAGEMENT_TYPE;
        $engagementType = EventEngagementType::create($data[$fieldName->value]);

        if ($engagementType->isUndefined()) {
            throw new FailActionOnModelException(
                'Invalid engagement ' . $data[$fieldName->value] . ' received',
                'attach engagement',
                Event::class
            );
        }

        DB::beginTransaction();
        try {
            if ($engagementType->isDetach()) {
                $event->engagingUsers()->detach($user->id);

                DB::commit();

                return $event;
            }

            $engagingUsers = $event->engagingUsers()->where('user_id', '=', $user->id)->get();

            if ($engagingUsers->isEmpty()) {
                $event->engagingUsers()
                    ->withPivotValue($fieldName->snakeCase(), $data[$fieldName->value])
                    ->attach($user->id);

                DB::commit();

                return $event;
            }

            $event->engagingUsers()->updateExistingPivot(
                $user->id,
                [$fieldName->snakeCase() => $data[ $fieldName->value ]]
            );

            DB::commit();
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException(
                $error->getMessage(),
                'attach engagement',
                Event::class
            );
        }

        return $event;
    }
}
