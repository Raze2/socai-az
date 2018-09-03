<?php

namespace App\Http\Controllers;

use App\Friendship;
use Illuminate\Http\Request;
use App\User;
use Auth;

class FriendshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function friends()
    {
        $user = Auth::user();
        return $user->friends->toJson();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestsSend()
    {
        $user = Auth::user();
        return $user->allFriendsOfThisUser()->wherePivot('status', 'pending')->get()->toJson();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestsRecived()
    {
        $user = Auth::user();
        return $user->allThisUserFriendOf()->wherePivot('status', 'pending')->get()->toJson();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sentRequest($id)
    {
        $user = Auth::user();
        $friendship = new Friendship;
        $friendship->first_user = $user->id;
        $friendship->acted_user = $user->id;
        $friendship->second_user = $id;
        $friendship->status = 'pending';
        $friendship->save();


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
        $friend = User::find($id);

        if($user->all_friends->whereIn('pivot.status', ['pending', 'confirmed'])->find($id)){
            $isFriend = $user->all_friends->find($id)->pivot;
            return ['name' => $friend->name,'photo_url' => $friend->photo_url, 'friendship' => $isFriend];
        } elseif ($user->all_friends->find($id)) {
             return response([], 500);
        } else {
            $isFriend = False;
            return ['name' => $friend->name,'photo_url' => $friend->photo_url, 'friendship' => $isFriend];
        }

        // $isFriend = ($user->requestSend->where('confirmed', 1)->find($id) OR $user->friendRequest->where('confirmed', 1)->find($id) ? 'friend' : ($user->requestSend->where('confirmed', 0)->find($id) ? 'sended' : ($user->friendRequest->where('confirmed', 0)->find($id) ? 'recived' : False)));

        // $isFriend = ($user->requestSend->find($id)->pivot->confirmed == 1) OR ($user->friendRequest->find($id)->pivot->confirmed == 1) ? 3 : ($user->requestSend->find($id)->pivot->confirmed == 0) ? 2 : ($user->friendRequest->find($id)->pivot->confirmed == 0) ? 1 : 0;

        // return $user->requestSend->find($friend->id)->pivot->id;
        // return $user->friendRequest->find($friend->id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function userAccept($id)
    {
        $user = Auth::user();
        $friendshipId = $user->allThisUserFriendOf()->wherePivot('status', 'pending')->findOrFail($id)->pivot->id;
        $friend = Friendship::find($friendshipId);

        // $friend = Friend::where('friend_id', $user->id)->where('confirmed', 0)->findOrFail($id);

        // $friend = $user->friendRequest()->wherePivot('id', $id)->get();
        // $req = Friend::where('id', $id)->where('user_id', $friend->id)->where('friend_id', $user->id)->get();
        // $req = $request->user()->friend()->get();

        $friend->status = 'confirmed';
        $friend->save();


        return response([], 204);    
    }

    // public function profileUserAccepted($id)
    // {
    //     $user = Auth::user();
    //     $friendshipId = $user->allThisUserFriendOf()->wherePivot('status', 'pending')->findOrFail($id)->pivot->id;
    //     $friend = Friendship::find($friendshipId);
    //     // $friend = $user->friendRequest()->wherePivot('id', $id)->get();
    //     // $req = Friend::where('id', $id)->where('user_id', $friend->id)->where('friend_id', $user->id)->get();
    //     // $req = $request->user()->friend()->get();
    //     $friend->status = 'confirmed';
    //     $friend->save();


    //     return response([], 204);   
    // }


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
            $friend = Friendship::where('first_user', $user->id)->orWhere('second_user', $user->id)->findOrFail($id);
            $friend->delete();
            return response([],204);
        } catch (\Exception $e) {
            return response(['Problem deleting'], 500);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    // public function profileDestroy($id)
    // {
    //     try{
    //         $user = Auth::user();
    //         $friend = Friend::where(function($q) use ($user, $id) {
    //             $q->where('friend_id', $user->id)->where('user_id', $id);
    //         }
    //         )->orWhere(function($s) use ($user, $id){
    //             $s->where('user_id', $user->id)->orWhere('friend_id', $id);
    //         }
    //         )->firstOrFail();
    //         $friend->delete();
    //         return response([],204);
    //     } catch (\Exception $e) {
    //         return response(['Problem deleting'], 500);
    //     }

    //     return $request;
        
    // }
}
