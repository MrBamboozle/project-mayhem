<?php

namespace App\Models;

use App\Exceptions\Exceptions\CannotImplementThisMethodException;
use App\Traits\ToCamelCaseArray;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserNotification extends Model
{
    use HasFactory, HasUuids, ToCamelCaseArray;

    protected $hidden = [
        'pivot'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_notification_user',
            'user_notification_id',
            'user_id'
        )
            ->using(UserNotificationUser::class)
            ->withPivot(['read']);
    }

    public function userNotificationUsers(): HasMany
    {
        return $this->hasMany(UserNotificationUser::class, 'user_notification_id', 'id');
    }

    public function changeBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'change_by', 'id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    /**
     * @throws CannotImplementThisMethodException
     */
    public function toArray(): array
    {
        return $this->toCamelCaseArray();
    }
}
