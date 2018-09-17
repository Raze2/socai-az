<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Like;
Use Auth;

class Post extends Model
{
    protected $fillable = [
        'body', 'user_id', 'privacy',
    ];

    protected $appends = [
        'auth_liked',
    ];
    
    
    public function user()
	{
    	return $this->belongsTo('App\User');
	}

	/**
     * Get the post for this user.
     */
    public function likes()
    {
        return $this->hasMany('App\Like');
    }

    /**
     * Get the post for this user.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function getAuthLikedAttribute()
    {
        $user = Auth::user();
        
        $like = $this->likes->where('user_id', $user->id)->first();
        
        if ($like) {
            return true;
        }
        
        return false;
    }


    public function getCreatedAtAttribute($value) {
    	return Carbon::parse($value)->diffForHumans();
    } 

    public function getUpdatedAtAttribute($value) {
    	return Carbon::parse($value)->diffForHumans();
    } 

    public function addLike() {
         return Like::create([
            'post_id'   => $this->id,
            'user_id'   => Auth::id()
         ]);
    }
}
