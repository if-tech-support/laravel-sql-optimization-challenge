<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'sku' => strtoupper(Str::random(10)),
            'price' => fake()->numberBetween(300, 50000),
            'stock' => fake()->numberBetween(0, 500),
            'description' => fake()->paragraph(),
            'is_published' => fake()->boolean(90),
        ];
    }
}
