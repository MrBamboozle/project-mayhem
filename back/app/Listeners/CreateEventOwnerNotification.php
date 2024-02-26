<?php

namespace App\Listeners;

use App\Enums\EventEngagementType;
use App\Events\EventEngagement;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class CreateEventOwnerNotification
{
    public function handle(EventEngagement $event): void
    {
        /** @var User $user */
        $user = Auth::user();
        $eventModel = $event->event;
        $eventTitle = $eventModel->title;
        $interaction = $event->interaction;

        if ($interaction->isUndefined()) {
            return;
        }

        $userNotification = UserNotification::factory()->createOne([
            'title' => "$user->name interacted with event $eventTitle",
            'description' => $this->getDescription($interaction, $user, $eventTitle),
            'user_id' => $user->id,
            'event_id' => $eventModel->id,
        ]);

        $userNotification->users()->withPivotValue('read', 0)->attach($eventModel->owner()->id);
    }

    private function getDescription(EventEngagementType $interaction, User $user, string $eventTitle): string
    {
        if ($interaction->isDetach()) {
            return "$user->name unsubscribed from event: $eventTitle";
        }

        return "$user->name marked $interaction->value on event: $eventTitle";
    }
}
