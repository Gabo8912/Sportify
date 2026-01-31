<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute; 
use App\Models\Profile;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    // <--- 2. IMPORTANTE: AGREGAR ESTE APPENDS
    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->profile?->profile_photo_path;

                if ($path) {
                    return asset('storage/' . $path);
                }

                $name = urlencode($this->name);
                return 'https://ui-avatars.com/api/?name=' . $name . '&color=7F9CF5&background=EBF4FF';
            },
        );
    }

    
    public function videos(){
        return $this->hasMany(Video::class)->latest();
    }

public function following() {
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
                ->withTimestamps();
}

public function followers() {
    return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                ->withTimestamps();
}

public function isFollowing($userId)
{
    return $this->following()->where('followed_id', $userId)->exists();
}

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });

        $query->when($filters['position'] ?? null, function ($query, $position) {
            $query->whereHas('profile', function ($q) use ($position) {
                $q->where('position', $position);
            });
        });

        $query->when($filters['foot'] ?? null, function ($query, $foot) {
            $query->whereHas('profile', function ($q) use ($foot) {
                $q->where('dominant_foot', $foot);
            });
        });
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}