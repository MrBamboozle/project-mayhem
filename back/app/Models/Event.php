<?php

namespace App\Models;

use App\Models\Interfaces\OwnedModel;
use App\Traits\ToCamelCaseArray;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model implements OwnedModel
{
    use HasFactory, HasUuids, ToCamelCaseArray;

    protected $fillable = [
        'city_id'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_event',
            'event_id',
            'category_id',
        )->using(CategoryEvent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function toArray(): array
    {
        return $this->toCamelCaseArray();
    }

    public function owner(): User
    {
        return $this->creator;
    }

    public function engagingUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'event_engagement',
            'event_id',
            'user_id'
        )
            ->using(EventEngagement::class)
            ->withPivot([
                'engagement_type',
                'updated_at',
                'created_at'
            ]);
    }

    public function engagingUsersTypes(): HasMany
    {
        return $this->hasMany(
            EventEngagement::class,
            'event_id',
            'id'
        );
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'event_id', 'id');
    }
}
