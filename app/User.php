<?php

namespace Social;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Social\Notifications\ResetPassword as ResetPasswordNotification;
use Laravel\Scout\Searchable;
use Social\Traits\Friendable;
use Auth;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Friendable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar','cover',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url', 
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        // return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
        return '../img/profile/'. $this->avatar;
    }


    /**
     * Get the Is Friend attribute.
     *
     * @return string
     */

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Get the post for this user.
     */
    public function posts()
    {
        return $this->hasMany('Social\Post');
    }

    public function addPost($request) {

        $post = Post::create([
            'body' => $request->body,
            'privacy' => $request->privacy,
            'user_id' => $this->id,
            'photo_url' => $request->photo_url,
        ]);

        return $post;

    }

    /**
     * Get the likes for this user.
     */
    public function likes()
    {
        return $this->hasMany('Social\Like');
    }

    /**
     * Get the Comments for this user.
     */
    public function comments()
    {
        return $this->hasMany('Social\Comment');
    }

         // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($user) { // before delete() method call this
             $user->posts()->delete();
             $user->likes()->delete();
             $user->comments()->delete();
             $user->allFriendsOfThisUser()->delete();
             $user->allThisUserFriendOf()->delete();
             // do the rest of the cleanup...
        });
    }

}
