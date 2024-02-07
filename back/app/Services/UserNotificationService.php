<?php

namespace App\Services;

use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Exceptions\Exceptions\FailToAddAvatarException;
use App\Exceptions\Exceptions\FailToDeleteCurrentAvatar;
use App\Models\Avatar;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserNotificationUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserNotificationService
{
    /**
     * @throws FailActionOnModelException
     */
    public function updateNotification(UserNotification $userNotification): void
    {
        DB::beginTransaction();
        try {
            $userNotificationUser = $userNotification
                ->userNotificationUsers()
                ->where(
                    'user_id',
                    '=',
                    Auth::user()->id
                )->first();

            if ($userNotificationUser->read === 0) {
                $userNotificationUser->read = 1;
                $userNotificationUser->save();
            }
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException(
                $error->getMessage(),
                'read notification',
                UserNotification::class
            );
        }

        DB::commit();
    }

    /**
     * @throws FailActionOnModelException
     */
    public function updateAllNotifications(): void
    {

        $user = Auth::user();
        $notifications = $user
            ->notificationsData()
            ->wherePivot(
                'read',
                '=',
                0
            )->get();

        DB::beginTransaction();
        try {
            $notifications->each(
                fn (UserNotification $notification) =>
                $user->notificationsData()
                    ->updateExistingPivot(
                        $notification->id,
                        ['read' => 1]
                    )
            );
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException(
                $error->getMessage(),
                'read all notifications',
                UserNotification::class
            );
        }

        DB::commit();
    }

    /**
     * @throws FailActionOnModelException
     */
    public function deleteNotification(UserNotification $userNotification): void
    {
        DB::beginTransaction();
        try {
            $userNotificationUser = $userNotification
                ->userNotificationUsers()
                ->where(
                    'user_id',
                    '=',
                    Auth::user()->id
                )->first();

            $userNotificationUser->delete();
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException(
                $error->getMessage(),
                'delete notification',
                UserNotification::class
            );
        }

        DB::commit();
    }

    /**
     * @throws FailActionOnModelException
     */
    public function deleteAllNotifications(): void
    {

        $user = Auth::user();
        $notifications = $user->userNotifications()->get();

        DB::beginTransaction();
        try {
            $notifications->each(fn (UserNotificationUser $notification) => $notification->delete());
        } catch (Throwable $error) {
            DB::rollBack();

            throw new FailActionOnModelException(
                $error->getMessage(),
                'delete all notifications',
                UserNotification::class
            );
        }

        DB::commit();
    }
}
