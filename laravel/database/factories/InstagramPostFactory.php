<?php

namespace Database\Factories;

use App\Models\InstagramPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstagramPost>
 */
class InstagramPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => 'https://www.instagram.com/p/'.fake()->unique()->regexify('[A-Za-z0-9_-]{11}').'/',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
