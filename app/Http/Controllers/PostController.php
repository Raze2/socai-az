<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\User;
use App\Like;
use Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $id = Auth::id();

        // $posts = Post::whereIn('user_id', function($query) use($id)
        // {
        //   $query->select('first_user')
        //         ->from('friendships')
        //         ->where('second_user', $id)
        //         ->where('status', 'confirmed');
        // })->orWhereIn('user_id', function($query) use($id)
        // {
        //   $query->select('second_user')
        //         ->from('friendships')
        //         ->where('first_user', $id)
        //         ->where('status', 'confirmed');
        // })->orWhere('user_id', $id)->with('user')->latest()->get();

        $userIds = $user->friends->pluck('id')->toArray();
        $posts = Post::whereIn('user_id', $userIds)->whereIn('privacy', ['public','friends'])->orWhere('user_id', $user->id)->with('user','likes')->latest()->get(); 

        // $posts = array();
        // foreach ($user->friends as $friend) {
        //     foreach($friend->posts as $post){
        //         $posts[] = $post;
        //     }
        // }
        return $posts;
    }

    public function profilePosts($id)
    {
        $user = User::find(1);        

        if($user->all_friends->find($id)){
            if($user->friends->find($id)) {
                $posts = Post::where('user_id', $id)->whereIn('privacy', ['public','friends'])->with('user')->latest()->get();
            } elseif ($user->all_friends->where('pivot.status', 'blocked')->find($id)) {
                $posts = response([],500);
            } 
        } else {
             if ($user->id == $id) {
                $posts = Post::where('user_id', $id)->whereIn('privacy', ['public','friends','private'])->with('user')->latest()->get();
            } else {
                $posts = Post::where('user_id', $id)->where('privacy', 'public')->with('user')->latest()->get();
            }
        }
        

        return $posts;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required|min:20',
            'privacy' => 'required',
        ]);

        $post = Auth::user()->addPost($request);

        // $post = new Post;
        // $post->body = $request->body;
        // $post->privacy = $request->privacy;
        // $post->user_id = Auth::id();
        // $post->save();
        $post->user = Auth::user();

        return $post;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function addLike($id)
    {
        $user = Auth::user();
        $userIds = $user->friends->pluck('id')->toArray();
        $posts = Post::whereIn('user_id', $userIds)->whereIn('privacy', ['public','friends'])->orWhere('user_id', $user->id)->get();
        $post = $posts->find($id);

        if($post){
            if($like = Like::where('user_id', $user->id)->where('post_id', $post->id)->first()) {
                $like->delete();
                return response([],204);
            } else {
                $like = $post->addLike();
                return $like;
            }
        }        

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $user = Auth::user();
        $post = $user->posts->find($id);

        $this->validate($request, [
            'body' => 'required|min:20',
            'privacy' => 'required',
        ]);

        $post->body = $request->body;
        $post->privacy = $request->privacy;
        $post->save();

        $post->user = Auth::user();

        return Post::with('user')->find($post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        try{
            $user = Auth::user();
            $post = $user->posts->find($id);
            $post->delete();
            return response([],204);
        } catch (\Exception $e) {
            return response(['Problem deleting'], 500);
        }
    }
}
