<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'path',
        'default',
    ];

    public function path(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Storage::url($value),
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'avatar_id', 'id');
    }
}
