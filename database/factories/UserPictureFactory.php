<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPicture>
 */
class UserPictureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_url' => 'https://loremflickr.com/500/500/face,person?random=' . rand(1, 999),
            'order' => fake()->numberBetween(0, 3),
        ];
    }
}
