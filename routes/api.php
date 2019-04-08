<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('friends', 'FriendshipController@friends');
    Route::get('friends/friendrequests', 'FriendshipController@requestsSend');
    Route::get('friends/requestsrecived', 'FriendshipController@requestsRecived');
    Route::get('friends/blockedusers', 'FriendshipController@blockedUsers');
    Route::get('friends/{id}', 'FriendshipController@profile');
    Route::get('friends/search/{search?}', 'FriendshipController@search');
    Route::post('friends/{id}', 'FriendshipController@sentRequest');
    Route::put('friends/{id}', 'FriendshipController@userAccept');
    Route::patch('friends/{id}', 'FriendshipController@userBlock');
    Route::delete('friends/{id}', 'FriendshipController@destroy');

    Route::get('posts', 'PostController@index');
    Route::get('posts/{id}', 'PostController@profilePosts');
    Route::get('posts/like/{id}', 'PostController@addLike');
    Route::post('post', 'PostController@store');
    Route::patch('post/{id}', 'PostController@update');
    Route::delete('post/{id}', 'PostController@destroy');

    Route::get('post/{id}/comments', 'CommentController@index');
    Route::post('post/{id}/comment', 'CommentController@store');
    Route::patch('post/{id}/comment/{comment_id}', 'CommentController@update');
    Route::delete('post/{id}/comment/{comment_id}', 'CommentController@destroy');

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    

});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});
