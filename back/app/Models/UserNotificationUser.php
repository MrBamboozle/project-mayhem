<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserNotificationUser extends Pivot
{
    use HasUuids;

    protected $fillable = [
        'read'
    ];

    protected $hidden = [
        'pivot'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function userNotification(): BelongsTo
    {
        return $this->belongsTo(UserNotification::class, 'user_notification_id', 'id');
    }
}
