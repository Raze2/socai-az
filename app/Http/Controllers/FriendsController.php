<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Friend;
use App\User;
use Auth;


class FriendsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function friends()
    {
        $user = Auth::user();
        return $user->friends()->toJson();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestsSend()
    {
        $user = Auth::user();
        return $user->requestSend()->where('confirmed', 0)->get()->toJson();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestsRecived()
    {
        $user = Auth::user();
        return $user->friendRequest()->where('confirmed', 0)->get()->toJson();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendRequest($id)
    {
        $user = Auth::user();
        $friend = new Friend;
        $friend->user_id = $user->id;
        $friend->friend_id = $id;
        $friend->save();


        return response([], 204);    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function profile($id)
    {
        $user = Auth::user();
        $friend = User::findOrFail($id);
        $isFriend = ($user->requestSend->where('confirmed', 1)->find($id) OR $user->friendRequest->where('confirmed', 1)->find($id) ? 'friend' : ($user->requestSend->where('confirmed', 0)->find($id) ? 'sended' : ($user->friendRequest->where('confirmed', 0)->find($id) ? 'recived' : False)));

        return ['name' => $friend->name,'photo_url' => $friend->photo_url, 'friend' => $isFriend];
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function userAccepted($id)
    {
        $user = Auth::user();
        $friend = Friend::where('friend_id', $user->id)->where('confirmed', 0)->findOrFail($id);
        // $friend = $user->friendRequest()->wherePivot('id', $id)->get();
        // $req = Friend::where('id', $id)->where('user_id', $friend->id)->where('friend_id', $user->id)->get();
        // $req = $request->user()->friend()->get();
        $friend->confirmed = True;
        $friend->save();


        return response([], 204);    
    }

    public function profileUserAccepted($id)
    {
        $user = Auth::user();
        $friend = Friend::where('friend_id', $user->id)->where('user_id', $id)->where('confirmed', 0)->firstOrFail();
        // $friend = $user->friendRequest()->wherePivot('id', $id)->get();
        // $req = Friend::where('id', $id)->where('user_id', $friend->id)->where('friend_id', $user->id)->get();
        // $req = $request->user()->friend()->get();
        $friend->confirmed = True;
        $friend->save();


        return response([], 204);    
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $user = Auth::user();
            $friend = Friend::where('friend_id', $user->id)->orWhere('user_id', $user->id)->findOrFail($id);
            $friend->delete();
            return response([],204);
        } catch (\Exception $e) {
            return response(['Problem deleting'], 500);
        }

        return $request;
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function profileDestroy($id)
    {
        try{
            $user = Auth::user();
            $friend = Friend::where(function($q) use ($user, $id) {
                $q->where('friend_id', $user->id)->where('user_id', $id);
            }
            )->orWhere(function($s) use ($user, $id){
                $s->where('user_id', $user->id)->orWhere('friend_id', $id);
            }
            )->firstOrFail();
            $friend->delete();
            return response([],204);
        } catch (\Exception $e) {
            return response(['Problem deleting'], 500);
        }

        return $request;
        
    }
}
