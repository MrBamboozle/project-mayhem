<?php

namespace App\Listeners;

use App\Events\EventUpdated;
use App\Models\Event;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateUserNotification
{
    private const SKIP_FIELDS = [
        'address',
        'created_at',
        'updated_at',
    ];

    public function __construct() {}

    public function handle(EventUpdated $event): void
    {
        $eventModel = $event->event;

        $changedValues = $this->detectObjectChanges($eventModel, $event->oldValues);

        if (empty($changedValues)) {
            return;
        }

        $eventTitle = $event->oldValues['title'];
        $eventUsers = $eventModel->engagingUsers;
        $description = $this->generateDescription($changedValues);
        $user = Auth::user();
        $userNotification = UserNotification::factory()->createOne([
            'title' => "$user->name changed event $eventTitle",
            'description' => $description,
            'user_id' => $user->id,
            'event_id' => $eventModel->id,
        ]);
        $engagedUsersIds = $eventUsers->map(fn (User $user) => $user->id);
        $userNotification->users()->withPivotValue('read', 0)->attach($engagedUsersIds);
    }

    private function detectObjectChanges(Event $event, array $oldValues): array
    {
        $changes = [];

        foreach ($oldValues as $camelKey => $value) {
            $key = Str::snake($camelKey);
            $data = $event->{$key};

            if (in_array($key, self::SKIP_FIELDS)) {
                continue;
            }

            if ($data instanceof Model) {
                if ($data->id !== $value['id']) {
                    $changes[$key] ='changed ' . $value['name'] . " to $data->name";
                }

                continue;
            }

            if ($data instanceof Collection) {
                $newPreparedValues = $this->extractIdAsKey($data);
                $oldPreparedValues = $this->extractIdAsKey(collect($value));

                foreach ($newPreparedValues as $newValueKey => $newValue) {
                    $oldValueExist = $oldPreparedValues->get($newValueKey);

                    if (empty($oldValueExist)) {
                        $changes[$key][$newValueKey] = "added $newValue->name";
                    }
                }

                foreach ($oldPreparedValues as $oldValueKey => $oldValue) {
                    $newValueExists = $newPreparedValues->get($oldValueKey);

                    if (empty($newValueExists)) {
                        $changes[$key][$oldValueKey] = 'deleted ' . $oldValue['name'];
                    }
                }

                continue;
            }

            if ($data !== $value) {
                $changes[$key] = "changed from $value to $data";
            }
        }

        return $changes;
    }

    private function extractIdAsKey(SupportCollection $collection): SupportCollection
    {
        return $collection->mapWithKeys(function (Model|array|string $value) {
            if (is_string($value)) {
                return [$value => $value];
            }

            if ($value instanceof Model) {
                return [$value->id => $value];
            }

            return [$value['id'] => $value];
        });
    }

    private function generateDescription(array $changedValues, ?string $ownerKey = null): string
    {
        $generatedString = '';
        $lastKey = array_key_last($changedValues);

        foreach ($changedValues as $key => $changedValue) {
            if (is_array($changedValue)) {
                $generatedString .= $this->generateDescription($changedValue, Str::singular($key));

                continue;
            }

            if (empty($ownerKey)) {
                $generatedString .= "$key $changedValue ";

                if ($lastKey !== $key) {
                    $generatedString .= ', ';
                }

                continue;
            }

            $generatedString .= "$ownerKey $changedValue ";

            if ($lastKey !== $key) {
                $generatedString .= ', ';
            }
        }

        return $generatedString;
    }
}
