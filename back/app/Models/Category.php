<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory, HasUuids;

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'category_event',
            'category_id',
            'event_id',
        );
    }
}
