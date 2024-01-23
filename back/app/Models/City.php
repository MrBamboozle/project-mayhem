<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'country_subdivision_id',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'city_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }
}
