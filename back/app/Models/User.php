<?php

namespace App\Models;

use App\Models\Interfaces\OwnedModel;
use App\Traits\ToCamelCaseArray;
use Auth;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable implements OwnedModel
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, ToCamelCaseArray;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'tokens',
        'avatar_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $with = [
        'avatar:id,path',
        'role:id,name'
    ];

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(Avatar::class, 'avatar_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param int|null $parentId
     * @param array $abilities
     * @param DateTimeInterface|null $expiresAt
     * @return NewAccessToken
     */
    public function createToken(
        string $name,
        string $parentId = null,
        array $abilities = ['*'],
        DateTimeInterface $expiresAt = null
    ): NewAccessToken
    {
        $plainTextToken = $this->generateTokenString();

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
            'parent_id' => $parentId,
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $loggedInUser = Auth::user();

        if (!$loggedInUser?->role->enum()->isGodMode() && $loggedInUser?->id !== $this->id) {
            unset($data['email']);
        }

        return $this->camelize($data);
    }

    public function owner(): User
    {
        return $this;// TODO: Implement owner() method.
    }

    public function engagingEvents(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'event_engagement',
            'user_id',
            'event_id'
        )->using(EventEngagement::class);
    }

    public function notificationsData(): BelongsToMany
    {
        return $this->belongsToMany(
            UserNotification::class,
            'user_notification_user',
            'user_id',
            'user_notification_id'
        )
            ->using(UserNotificationUser::class)
            ->withPivot(['read']);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotificationUser::class, 'user_id', 'id');
    }
}
