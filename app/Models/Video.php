<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'video_url',
        'description', 
        'platform',          // <--- ESTE ES OBLIGATORIO
        'external_video_id', // <--- ESTE ES OBLIGATORIO
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

}
