<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\CheckUserAccess;

class UserPolicy
{
    use CheckUserAccess;

    public function create(User $user): bool
    {
        if ($user->role->enum()->isGodMode()) {
            return true;
        }

        return false;
    }

    public function update(User $user, User $model): bool
    {
        return $this->userHasAccess($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->userHasAccess($user, $model);
    }

    public function resetPassword(User $user, User $model): bool
    {
        return $this->userHasAccess($user, $model);
    }

    public function addAvatar(User $user, User $model): bool
    {
        return $this->userHasAccess($user, $model);
    }
}
