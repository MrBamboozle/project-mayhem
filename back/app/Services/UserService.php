<?php

namespace App\Services;

use App\Exceptions\Exceptions\FailToAddAvatarException;
use App\Exceptions\Exceptions\FailToDeleteCurrentAvatar;
use App\Models\Avatar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserService
{
    /**
     * @throws FailToAddAvatarException
     * @throws FailToDeleteCurrentAvatar
     */
    public function createUser(array $data): User
    {
        $user = User::factory($data)->unverified()->make();
        $user->role_id = Role::where('name', '=', \App\Enums\Role::REGULAR->value)->get()->first()->id;

        return $this->updateAvatar($user, $this->getDefaultAvatar());
    }

    /**
     * @throws FailToAddAvatarException
     * @throws FailToDeleteCurrentAvatar
     */
    public function updateAvatar(User $user, ?Avatar $avatar, UploadedFile $file = null): User
    {
        if (empty($avatar) && empty($file)) {
            throw new FailToAddAvatarException('Avatar file or id missing');
        }

        // We have defined $with on User model which always returns avatar without all values,
        // this way we get the whole Avatar object
        // if we don't do this Avatar object won't have "default" field and this will fail
        /** @var Avatar $currentAvatar */
        $currentAvatar = $user->avatar()->first();
        $user = $this->updateUserAvatar($user, $avatar, $file);
        $this->deleteCurrentAvatar($currentAvatar);

        return $user;
    }

    private function getDefaultAvatar(): Avatar
    {
        /** @var Collection $avatars */
        $avatars = Avatar::where('default', true)->get();

        return $avatars[mt_rand(0, count($avatars) - 1)];
    }

    /**
     * @throws FailToAddAvatarException
     */
    private function updateUserAvatar(User $user, ?Avatar $avatar, UploadedFile $file = null): User
    {
        DB::beginTransaction();

        try {
            if (empty($avatar)) {
                $avatar = Avatar::factory([
                    'path' => $file->store('public/avatars'),
                    'default' => false,
                ])->create();
            }

            $user->avatar()->associate($avatar);
            $user->save();
        } catch (Throwable $error) {
            $path = $avatar->getRawOriginal('path');

            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            DB::rollBack();

            throw new FailToAddAvatarException($error->getMessage());
        }

        DB::commit();

        return $user;
    }

    /**
     * @throws FailToDeleteCurrentAvatar
     */
    private function deleteCurrentAvatar(?Avatar $currentAvatar): void
    {
        if (empty($currentAvatar)) {
            return;
        }

        DB::beginTransaction();
        try {
            // getRawOriginal() bypasses the defined "path" accessor on Avatar model
            if (!$currentAvatar->default) {
                $currentAvatar->delete();
                Storage::delete($currentAvatar->getRawOriginal('path'));
            }
        } catch (Throwable $error) {
            throw new FailToDeleteCurrentAvatar($error->getMessage());
        }
    }
}
