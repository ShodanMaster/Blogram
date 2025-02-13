<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    use HasFactory;

    protected $fillable = ['follower_id', 'followed_id'];

    public function followedUser()
    {
        return $this->belongsTo(User::class, 'followed_id');
    }

    // Define the relationship to the follower user
    public function followerUser()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
