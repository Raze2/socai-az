<?php

namespace Social\Http\Controllers;

use Social\Comment;
use Auth;
use Social\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = Auth::user();
        $userIds = $user->friends->pluck('id')->toArray();
        $posts = Post::whereIn('user_id', $userIds)->whereIn('privacy', ['public','friends'])->orWhere('user_id', $user->id)->get();
        $post = $posts->find($id);

        $comments = Comment::where('post_id', $post->id)->with('user')->latest()->paginate(2);

        return $comments;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    { 
        $this->validate($request, [
            'body' => 'required',
        ]);

        $user = Auth::user();
        $userIds = $user->friends->pluck('id')->toArray();
        $posts = Post::whereIn('user_id', $userIds)->whereIn('privacy', ['public','friends'])->orWhere('user_id', $user->id)->get();
        $post = $posts->find($id);
        if($post)
            $comment = $post->addComment($request->body);

        $comment->user = $user;

        return $comment;
    }

    /**
     * Display the specified resource.
     *
     * @param  \Social\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Social\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Social\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update($id, $comment_id, Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        $user = Auth::user();
        $comment = $user->comments->find($comment_id);
        $comment->body = $request->body;
        $comment->save();
        
        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Social\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $comment_id)
    {
        try{
            $user = Auth::user();
            $comment = $user->comments->find($comment_id);
            $comment->delete();
            return response([],204);
        } catch (\Exception $e) {
            return response(['Problem deleting'], 500);
        }
    }
}
