<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPlayer extends Model
{
    use HasFactory;
    protected $fillable = [
        'scout_id',
        'player_id',
        'scout_notes'
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
