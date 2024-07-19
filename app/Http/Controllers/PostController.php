<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image'
        ]);

        $post = new Post();
        $post->user_id = auth()->id();
        $post->content = $request->content;

        if ($request->hasFile('image')) {
            $post->image_url = $request->file('image')->store('posts', 'public');
        }

        $post->save();

        return response()->json($post, 201);
    }

    public function index()
    {
        $posts = Post::with(['user', 'comments', 'likes'])
            ->selectRaw('*, (likes_count * 2) + (comments_count * 1.5) + (UNIX_TIMESTAMP(created_at) / 100000) as score')
            ->orderByDesc('score')
            ->paginate(10);

        return response()->json($posts);
    }
}
