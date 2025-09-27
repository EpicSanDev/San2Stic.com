<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Comment;

class Sound extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'latitude',
        'longitude',
    ];

    /**
     * Get the user that owns the sound.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users that have liked this sound.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'sound_id', 'user_id');
    }

    /**
     * Get the comments for the sound.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}