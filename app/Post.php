<?php

namespace Social;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Social\Like;
use Social\Comment;
Use Auth;

class Post extends Model
{
    protected $fillable = [
        'body', 'user_id', 'privacy', 'photo_url',
    ];

    protected $appends = [
        'auth_liked', 'likes_num', 'comments_num'
    ];
    
    
    public function user()
	{
    	return $this->belongsTo('Social\User');
	}

	/**
     * Get the post for this user.
     */
    public function likes()
    {
        return $this->hasMany('Social\Like');
    }

    /**
     * Get the post for this user.
     */
    public function comments()
    {
        return $this->hasMany('Social\Comment');
    }

     // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($post) { // before delete() method call this
             $post->likes()->delete();
             $post->comments()->delete();
             // do the rest of the cleanup...
        });
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

    public function getLikesNumAttribute()
    {
        return $this->likes->count();
    }

    public function getCommentsNumAttribute()
    {
        return $this->comments->count();
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
    public function addComment($body) {
         return Comment::create([
            'post_id'   => $this->id,
            'body'   => $body,
            'user_id'   => Auth::id()
         ]);
    }
}
