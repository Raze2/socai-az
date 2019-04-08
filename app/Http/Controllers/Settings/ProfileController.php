<?php

namespace Social\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Social\Http\Controllers\Controller;
use Auth;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $currentPhoto = Auth::user()->avatar;

        if($request->photo != $currentPhoto){
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->resize(200, 200)->save(public_path('img/profile/').$name);
            $request->merge(['avatar' => $name]);
            $userPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($userPhoto) && $currentPhoto != 'user.png'){
                @unlink($userPhoto);
            }
        }

        $currentCover = Auth::user()->cover;

        if($request->cover != $currentCover){
            $coverName = time().'.' . explode('/', explode(':', substr($request->cover, 0, strpos($request->cover, ';')))[1])[1];
            \Image::make($request->cover)->resize(800, 400)->save(public_path('img/cover/').$coverName);
            $request->merge(['cover' => $coverName]);
            $userCover = public_path('img/cover/').$currentCover;
            if(file_exists($userCover) && $currentCover != 'cover.png'){
                @unlink($userCover);
            }
        }

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        return tap($user)->update($request->only('name', 'email', 'avatar', 'cover'));
    }
}
