<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'description' => $this->faker->sentence,
            'price' => 1000,
            'image_path' => 'items/sample.jpg',
            'status' => 'used',
        ];
    }

    public function sold()
    {
        return $this->state(fn(array $attributes) => ['is_sold' => true]);
    }
}
