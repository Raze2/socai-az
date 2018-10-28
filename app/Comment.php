<?php

namespace Social;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model
{	
	protected $fillable = [
        'body', 'user_id', 'post_id'
    ];
    
    public function user()
	{
    	return $this->belongsTo('Social\User');
	}

	public function post()
	{
    	return $this->belongsTo('Social\Post');
	}
	public function getCreatedAtAttribute($value) {
    	return Carbon::parse($value)->diffForHumans();
    } 

    public function getUpdatedAtAttribute($value) {
    	return Carbon::parse($value)->diffForHumans();
    } 
}
