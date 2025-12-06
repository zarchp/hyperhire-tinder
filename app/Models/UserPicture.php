<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserPicture extends Model
{
    /** @use HasFactory<\Database\Factories\UserPictureFactory> */
    use HasFactory;

    protected $fillable = [
        'image_url',
        'order',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
