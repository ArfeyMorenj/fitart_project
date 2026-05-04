<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductGroup;
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
            'code' => 'PRD' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => fake()->words(3, true),
            'specification' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'author_code' => fake()->numerify('AC###'),
            'author_name' => fake()->name(),
            'compiler' => fake()->name(),
            'year' => (string) fake()->year(),
            'product_group_id' => ProductGroup::query()->inRandomOrder()->value('id'),
            'is_active' => true,
        ];
    }
}