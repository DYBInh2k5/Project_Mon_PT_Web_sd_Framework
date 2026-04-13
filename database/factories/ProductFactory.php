<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'product_category_id' => ProductCategory::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->unique()->bothify('PRD-###??')),
            'price' => fake()->randomFloat(2, 10, 5000),
            'stock' => fake()->numberBetween(0, 100),
            'description' => fake()->sentence(),
            'image_path' => null,
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
