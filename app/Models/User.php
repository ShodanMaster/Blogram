<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
        'google_id',
        'ban'
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

    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function blogs(){
        return $this->hasMany(Blog::class);
    }

    public function likedBlogs()
    {
        return $this->belongsToMany(Blog::class)->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Define the followers relationship
    public function followers()
    {
        return $this->hasMany(UserFollow::class, 'followed_id');
    }

    // Define the users the current user follows
    public function following()
    {
        return $this->hasMany(UserFollow::class, 'follower_id');
    }

    // Define the method to check if the user is following another user
    public function isFollowing($userId)
    {
        return $this->following()->where('followed_id', $userId)->exists();
    }

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }

}
