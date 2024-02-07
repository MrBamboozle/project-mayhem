<?php

namespace App\Policies;

use App\Enums\Route;
use App\Models\Event;
use App\Models\User;
use App\Traits\CheckUserAccess;

class EventPolicy
{
    use CheckUserAccess;

    public function create(User $user): bool
    {
        $userRoleEnum = $user->role->enum();

        if ($userRoleEnum->isGodMode() || $userRoleEnum->isAdmin() || $userRoleEnum->isPremium()) {
            return true;
        }

        return !Route::create(request()->path())->isEventPrivate();
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->role->enum()->isGodMode() || $user->role->enum()->isAdmin()) {
            return true;
        }

        return $this->userHasAccess($user, $event);
    }

   public function delete(User $user, Event $event): bool
    {
        return $this->userHasAccess($user, $event);
    }
}
