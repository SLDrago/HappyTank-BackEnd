<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch users who can create posts (role = 'user' or 'shop')
        $users = User::whereIn('role', ['shop', 'user'])->get();

        if ($users->isEmpty()) {
            $this->command->info('No users with role "shop" or "user" found. Please seed appropriate users first.');
            return;
        }

        // Create 10 sample posts
        Post::factory()->count(10)->create()->each(function ($post) use ($users) {
            // Add random comments to each post
            Comment::factory()->count(rand(3, 7))->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ])->each(function ($comment) use ($users) {
                // Add random replies to each comment
                Comment::factory()->count(rand(1, 3))->create([
                    'post_id' => $comment->post_id,
                    'user_id' => $users->random()->id,
                    'parent_id' => $comment->id,
                ]);

                // Add random likes to each comment
                $commentLikers = $users->random(rand(0, 5));
                foreach ($commentLikers as $user) {
                    if (!Like::where('user_id', $user->id)->where('likeable_id', $comment->id)->where('likeable_type', Comment::class)->exists()) {
                        Like::create([
                            'user_id' => $user->id,
                            'likeable_id' => $comment->id,
                            'likeable_type' => Comment::class,
                        ]);
                    }
                }
            });

            // Add random likes to each post
            $postLikers = $users->random(rand(5, 15));
            foreach ($postLikers as $user) {
                if (!Like::where('user_id', $user->id)->where('likeable_id', $post->id)->where('likeable_type', Post::class)->exists()) {
                    Like::create([
                        'user_id' => $user->id,
                        'likeable_id' => $post->id,
                        'likeable_type' => Post::class,
                    ]);
                }
            }
        });

        $this->command->info('Sample posts with comments and likes have been created.');
    }
}
