<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class LikeController extends Controller
{
    public function likePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id'
        ]);

        $post = Post::find($request->post_id);

        if ($post->likes()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Post already liked'], 409);
        }

        $post->likes()->create(['user_id' => auth()->id()]);
        $post->increment('likes_count');
        return response()->json(['message' => 'Post liked'], 201);
    }

    public function unlikePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id'
        ]);

        $post = Post::find($request->post_id);

        if (!$post->likes()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Post not liked'], 409);
        }

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
