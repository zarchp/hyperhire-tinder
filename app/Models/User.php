<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

final class User extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'age',
        'location',
        'is_popular_notified',
    ];

    protected $casts = [
        'is_popular_notified' => 'boolean',
    ];

    public function pictures(): HasMany
    {
        return $this->hasMany(UserPicture::class)->orderBy('order');
    }

    public function swipes(): HasMany
    {
        return $this->hasMany(Swipe::class);
    }

    public function intiatedSwipes(): HasMany
    {
        return $this->hasMany(Swipe::class, 'actor_user_id');
    }

    public function receivedSwipes(): HasMany
    {
        return $this->hasMany(Swipe::class, 'target_user_id');
    }
}
