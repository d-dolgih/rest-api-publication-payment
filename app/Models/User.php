<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    /**
     * @return HasMany
     */
    public function publications(): HasMany
    {
        return $this->hasMany(UserPublication::class);
    }

    /**
     * @return BelongsToMany
     */
    public function subscribe()
    {
        return $this->belongsTo(Subscribe::class)->where('active', true);
    }

    /**
     * @return mixed
     */
    public function publicationsLimit()
    {
        return $this->subscribe()->value('publications_limit') ?? 0;
    }

    /**
     * @return int
     */
    public function publicationsCount()
    {
        return $this->publications()->count();
    }

    /**
     * @return bool
     */
    public function canPublish()
    {
        return (!$this->isPublicationLimit() && !$this->isSubscriptionExpired());
    }

    /**
     * @return bool
     */
    public function isPublicationLimit()
    {
        return $this->publicationsCount() >= $this->publicationsLimit();
    }

    /**
     * @return bool
     */
    public function isSubscriptionExpired()
    {
        $now = Carbon::now();
        return (($this->subscribe_expire_at ?? $now) < $now);
    }
}
