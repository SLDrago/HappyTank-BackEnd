<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

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
            $post->image_url = Storage::url($request->file('image')->store('posts', 'public'));
        }

        $post->save();

        $post = Post::with(['user', 'comments.user', 'comments.replies.user', 'likes'])->find($post->id);

        return response()->json($post, 201);
    }


    public function index(Request $request)
    {
        $posts = Post::with(['user', 'comments.user', 'comments.replies.user', 'likes'])
            ->where('status', true)
            ->selectRaw('*, (likes_count * 2) + (comments_count * 1.5) + (UNIX_TIMESTAMP(created_at) / 100000) as score')
            ->orderByDesc('score')
            ->paginate(10);

        return response()->json($posts);
    }

    public function update(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
            'image_base64' => 'nullable|string',
            'remove_image' => 'nullable|boolean',
        ]);

        $post = Post::findOrFail($request->post_id);

        // Check if the authenticated user is the owner of the post
        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->content = $request->content;

        if ($request->remove_image === true) {
            // Remove the old image if requested
            if ($post->image_url) {
                $oldImagePath = str_replace('/storage', 'public', $post->image_url);
                Storage::delete($oldImagePath);
                $post->image_url = null; // Set image_url to null in the database
            }
        } elseif ($request->image_base64) {
            // Handle the base64 image upload
            $imageData = explode(',', $request->image_base64);
            $decodedImage = base64_decode($imageData[1]);
            $imageName = 'posts/' . uniqid() . '.png';
            Storage::disk('public')->put($imageName, $decodedImage);
            $post->image_url = Storage::url($imageName);
        }

        $post->save();

        $post = Post::with(['user', 'comments.user', 'comments.replies.user', 'likes'])->find($post->id);

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Check if the authenticated user is the owner of the post
        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the image if it exists
        if ($post->image_url) {
            $imagePath = str_replace('/storage', 'public', $post->image_url);
            Storage::delete($imagePath);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function report(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'reason' => 'required|string'
        ]);

        $post = Post::findOrFail($request->post_id);

        DB::table('reported_content')->insert([
            'reporter_id' => auth()->id(),
            'content_id' => $post->id,
            'content_type' => 'Post',
            'report_reason' => $request->reason
        ]);

        return response()->json(['message' => 'Post reported successfully']);
    }

    public function dissablePost($id)
    {
        $post = Post::findOrFail($id);

        $post->status = false;
        $post->save();

        return response()->json(['message' => 'Post disabled successfully']);
    }

    public function enablePost($id)
    {
        $post = Post::findOrFail($id);

        $post->status = true;
        $post->save();

        return response()->json(['message' => 'Post enabled successfully']);
    }

    public function getPost($id)
    {
        $post = Post::findOrFail($id);
        return response()->json($post);
    }
}
