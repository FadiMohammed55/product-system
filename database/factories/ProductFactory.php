<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 2000),
            'currency' => 'USD',
            'rating' => fake()->randomFloat(1, 0, 5),
            'image' => null,
        ];
    }
}
