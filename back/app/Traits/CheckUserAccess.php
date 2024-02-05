<?php

namespace App\Traits;

use App\Models\Interfaces\OwnedModel;
use App\Models\User;

trait CheckUserAccess
{
    private function userHasAccess(User $user, OwnedModel $model): bool
    {
        if ($user->role->enum()->isGodMode()) {
            return true;
        }

        if ($user->id === $model->owner()->id) {
            return true;
        }

        return false;
    }
}
