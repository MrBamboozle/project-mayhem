<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use \Laravel\Sanctum\PersonalAccessToken as SanctumToken;

/**
 * @property int $id
 * @property int $parent_id
 * @property PersonalAccessToken $parentToken
 * @property PersonalAccessToken $childToken
 * @property DateTimeInterface $expires_at
 */
class PersonalAccessToken extends SanctumToken
{
    use HasFactory;

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
