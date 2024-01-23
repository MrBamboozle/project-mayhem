<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use \Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class PersonalAccessToken extends SanctumToken
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'parent_id',
    ];

    public function parentToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class, 'parent_id', 'id');
    }

    public function childToken(): HasOne
    {
        return $this->hasOne(PersonalAccessToken::class, 'parent_id', 'id');
    }
}
