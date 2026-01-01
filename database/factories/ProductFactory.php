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
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'sku' => strtoupper($this->faker->bothify('SKU-###')),
            'price' => $this->faker->numberBetween(5000, 50000),
            'cost_price' => $this->faker->numberBetween(3000, 30000),
            'image' => null,
            'description' => $this->faker->sentence(),
        ];
    }
}
