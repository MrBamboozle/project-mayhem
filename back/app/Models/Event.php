<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $title
 * @property string $tag_line
 * @property string $description
 * @property DateTime $time
 * @property string $location
 * @property string $user_id
 * @property string $city_id
 * @property User $creator
 * @property City $city
 * @property Category[] $categories
 */
class Event extends Model
{
    use HasFactory, HasUuids;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_event',
            'event_id',
            'category_id',
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
