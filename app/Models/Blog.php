<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function likedUsers(){

        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function comments(){

        return $this->hasMany(Comment::class)->latest();
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
