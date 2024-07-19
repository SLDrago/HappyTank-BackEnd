<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph,
            'image_url' => $this->faker->imageUrl(640, 480, 'fish', true, 'ornamental fish'),
            'likes_count' => 0,
            'comments_count' => 0,
        ];
    }
}
