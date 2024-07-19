<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = new Comment();
        $comment->user_id = auth()->id();
        $comment->post_id = $post->id;
        $comment->content = $request->content;
        $comment->parent_id = $request->parent_id;
        $comment->save();

        $post->increment('comments_count');

        return response()->json($comment, 201);
    }
}
