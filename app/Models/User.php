<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    //Bridge
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function videos(){
        return $this->hasMany(Video::class)->latest();
    }

    //My follows
    public function following(){
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    //My followers
    public function followers(){
        return $this->belongsToMany(User::class,'follows','followed_id','follower_id');
    }

    //Scouts
    public function savedPlayers(){
        return $this->hasMany(SavedPlayer::class, 'scout_id');
    }

}

