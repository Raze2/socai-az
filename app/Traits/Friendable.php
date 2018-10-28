<?php

namespace Social\Traits;

use Social\User;
use Social\Friendship;
use \Auth;

trait Friendable 
{

	public function addFriend($id, $status) {

		$friendship = Friendship::create([
			'first_user' => $this->id,
			'acted_user' => $this->id,
			'second_user' => $id,
			'status' => $status,
		]);

	}
	 // public function requestSend() {
    //     return $this->belongsToMany('Social\User', 'friends', 'user_id', 'friend_id')->withPivot('id', 'confirmed');
    // }
    // public function friendRequest() {
    //     return $this->belongsToMany('Social\User', 'friends', 'friend_id', 'user_id')->withPivot('id', 'confirmed');
    // }
    // public function friends() {
    //     $con1 = $this->requestSend()->where('confirmed', 1)->get();
    //     $con2 = $this->friendRequest()->where('confirmed', 1)->get();
    //     return $con1->merge($con2);
    // }

    // All friendship that this user has started
    public function allFriendsOfThisUser()
    {
        return $this->belongsToMany(User::class, 'friendships', 'first_user', 'second_user')
        ->withPivot('status','acted_user','id');      
    }
 
    // All friendship that this user is asked for
    public function allThisUserFriendOf()
    {
        return $this->belongsToMany(User::class, 'friendships', 'second_user', 'first_user')
        ->withPivot('status','acted_user', 'id');
    }

     // accessor allowing you call $user->friends
    public function getAllFriendsAttribute()
    {
        if ( ! array_key_exists('friends', $this->relations)) $this->loadAllFriends();
        return $this->getRelation('all_friends');
    }
 
    protected function loadAllFriends()
    {
        if ( ! array_key_exists('all_friends', $this->relations))
        {
        $friends = $this->mergeAllFriends();
        $this->setRelation('all_friends', $friends);
        }
    }
 
    protected function mergeAllFriends()
    {
        if($temp = $this->allFriendsOfThisUser)
        return $temp->merge($this->allThisUserFriendOf);
        else
        return $this->allThisUserFriendOf;
    }




    // friendship that this user started
    protected function friendsOfThisUser()
    {
        return $this->belongsToMany(User::class, 'friendships', 'first_user', 'second_user')
        ->withPivot('status','acted_user')
        ->wherePivot('status', 'confirmed');
    }
 
    // friendship that this user was asked for
    protected function thisUserFriendOf()
    {
        return $this->belongsToMany(User::class, 'friendships', 'second_user', 'first_user')
        ->withPivot('status','acted_user')
        ->wherePivot('status', 'confirmed');
    }
 
    // accessor allowing you call $user->friends
    public function getFriendsAttribute()
    {
        if ( ! array_key_exists('friends', $this->relations)) $this->loadFriends();
        return $this->getRelation('friends');
    }
 
    protected function loadFriends()
    {
        if ( ! array_key_exists('friends', $this->relations))
        {
        $friends = $this->mergeFriends();
        $this->setRelation('friends', $friends);
        }
    }
 
    protected function mergeFriends()
    {
        if($temp = $this->friendsOfThisUser)
        return $temp->merge($this->thisUserFriendOf);
        else
        return $this->thisUserFriendOf;
    }



    public function friend_requests()
    {
        return $this->hasMany(Friendship::class, 'second_user')
        ->where('status', 'pending');
    }

    public function requests_send()
    {
        return $this->hasMany(Friendship::class, 'first_user')
        ->where('status', 'pending');
    }

    

    // friendship that this user started but now blocked
    protected function friendsOfThisUserBlocked()
    {
        return $this->belongsToMany(User::class, 'friendships', 'first_user', 'second_user')
                    ->withPivot('status', 'acted_user')
                    ->wherePivot('status', 'blocked')
                    ->wherePivot('acted_user', 'first_user');
    }
 
    // friendship that this user was asked for but now blocked
    protected function thisUserFriendOfBlocked()
    {
        return $this->belongsToMany(User::class, 'friendships', 'second_user', 'first_user')
                    ->withPivot('status', 'acted_user')
                    ->wherePivot('status', 'blocked')
                    ->wherePivot('acted_user', 'second_user');
    }
 
    // accessor allowing you call $user->friends
    public function getBlockedFriendsAttribute()
    {
        if ( ! array_key_exists('blocked_friends', $this->relations)) $this->loadBlockedFriends();
            return $this->getRelation('blocked_friends');
    }
 
    protected function loadBlockedFriends()
    {
        if ( ! array_key_exists('blocked_friends', $this->relations))
        {
            $friends = $this->mergeBlockedFriends();
            $this->setRelation('blocked_friends', $friends);
        }
    }
 
    protected function mergeBlockedFriends()
    {
        if($temp = $this->friendsOfThisUserBlocked)
            return $temp->merge($this->thisUserFriendOfBlocked);
        else
            return $this->thisUserFriendOfBlocked;
    }


}