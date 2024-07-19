<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class LikeController extends Controller
{
    public function likePost(Post $post)
    {
        $post->likes()->create(['user_id' => auth()->id()]);
        $post->increment('likes_count');
        return response()->json(['message' => 'Post liked'], 201);
    }

    public function unlikePost(Post $post)
    {
        $post->likes()->where('user_id', auth()->id())->delete();
        $post->decrement('likes_count');
        return response()->json(['message' => 'Post unliked'], 204);
    }

    public function likeComment(Comment $comment)
    {
        $comment->likes()->create(['user_id' => auth()->id()]);
        $comment->increment('likes_count');
        return response()->json(['message' => 'Comment liked'], 201);
    }

    public function unlikeComment(Comment $comment)
    {
        $comment->likes()->where('user_id', auth()->id())->delete();
        $comment->decrement('likes_count');
        return response()->json(['message' => 'Comment unliked'], 204);
    }
}
