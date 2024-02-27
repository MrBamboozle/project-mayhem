<?php

namespace App\Http\Controllers;

use App\Enums\QueryField;
use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Models\UserNotification;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    public function __construct(
        protected readonly UserNotificationService $notificationService
    ) {}

    public function index(Request $request): array
    {
        $perPage = $request->query(QueryField::PER_PAGE->value);
        $user = Auth::user();
        $totalUnread = $user->userNotifications()->where('read', 0)->count();
        $paginator = $user->notificationsData()->orderByDesc('user_notifications.created_at')->paginate($perPage, [
            'user_notifications.id',
            'user_notifications.user_id',
            'user_notifications.title',
            'user_notifications.description',
            'user_notifications.event_id',
            'user_notification_user.read',
        ])->toArray();

        $paginator['totalUnread'] = $totalUnread;

        return $paginator;
    }

    /**
     * @throws FailActionOnModelException
     */
    public function update(UserNotification $userNotification): array
    {
        $this->notificationService->updateNotification($userNotification);

        return [
            'message' => "Notification $userNotification->title read",
        ];
    }

    /**
     * @throws FailActionOnModelException
     */
    public function updateAll(): array
    {
        $this->notificationService->updateAllNotifications();

        return [
            'message' => 'Successfully read all notifications'
        ];
    }

    /**
     * @throws FailActionOnModelException
     */
    public function destroy(UserNotification $userNotification): array
    {
        $this->notificationService->deleteNotification($userNotification);

        return [
            'message' => "Notification $userNotification->title deleted",
        ];
    }

    /**
     * @throws FailActionOnModelException
     */
    public function deleteAll(): array
    {
        $this->notificationService->deleteAllNotifications();
        return [
            'message' => 'Successfully deleted all notifications'
        ];
    }
}
