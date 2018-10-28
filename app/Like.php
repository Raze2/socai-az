<?php

namespace Social;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['post_id', 'user_id'];

    public function user()
	{
    	return $this->belongsTo('Social\User');
	}

	public function post()
	{
    	return $this->belongsTo('Social\Post');
	}
}
