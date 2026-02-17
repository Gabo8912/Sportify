<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Save;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'video_url',
        'description',
        'platform',
        'external_video_id',
        'views_count',
        'likes_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }
    public function comments() {
    return $this->hasMany(Comment::class)->latest();
    }

    public function saves():MorphMany {
        return $this->morphMany(Save::class,'saveable');
    }

    public function isSavedBy(?User $user): bool {
        if(! $user) return false;
        return $user->saves()-> where('user_id', $user->id)->exists();
    }


}
