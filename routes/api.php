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

    Route::get('friends', 'FriendsController@friends');
    Route::get('friends/friendrequests', 'FriendsController@requestsSend');
    Route::get('friends/requestsrecived', 'FriendsController@requestsRecived');
    Route::get('friends/{id}', 'FriendsController@profile');
    Route::post('friends/{id}', 'FriendsController@sendRequest');
    Route::put('friends/{id}', 'FriendsController@userAccepted');
    Route::put('friends/profile/{id}', 'FriendsController@profileUserAccepted');
    Route::delete('friends/{id}', 'FriendsController@destroy');
    Route::delete('friends/profile/{id}', 'FriendsController@profileDestroy');

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
