<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use App\Models\Client;
use App\Models\Product;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        return [
            'number' => 'WO-' . fake()->unique()->numerify('#####'),
            'date' => fake()->date(),
            'date_install' => fake()->date(),
            'start_license' => fake()->optional()->date(),

            'client_id' => Client::factory(),
            'product_id' => Product::query()->inRandomOrder()->value('id'),
            'item_id' => Item::query()->inRandomOrder()->value('id'),

            'status' => fake()->randomElement(['AKTIF', 'STOP', 'SELESAI']),
            'amount' => fake()->numberBetween(0, 20000000),
            'description' => fake()->sentence(),
            'item_count' => fake()->numberBetween(0, 12),
            'per_unit' => fake()->randomElement(['per-bulan', 'per-tahun', 'UNIT']),
            'notes' => fake()->sentence(),
        ];
    }
}