<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'subcategory_id' => Subcategory::factory(),
            'category_id' => fn (array $attributes): int => Subcategory::query()
                ->findOrFail($attributes['subcategory_id'])
                ->category_id,
            'name' => fake()->unique()->words(3, true),
            'slug' => fn (array $attributes): string => Str::slug($attributes['name']),
            'description' => fake()->paragraph(),
            'image_path' => null,
            'old_price' => fake()->randomFloat(2, 20, 500),
            'new_price' => fake()->randomFloat(2, 1, 19),
        ];
    }
}
