<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'position',
        'dominant_foot',
        'height',
        'weight',
        'birth_date',
        'current_club',
        'location',
        'achievements',
        'availability_status',
        'profile_photo_path',
        'cover_photo_path',
    ];
    
    //Connection with profile owner (the bridge)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
