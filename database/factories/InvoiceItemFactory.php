<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\MasterItemProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $mipId = MasterItemProduct::query()->inRandomOrder()->value('id');

        $qty = fake()->numberBetween(1, 10);
        $price = fake()->numberBetween(100000, 5000000);

        return [
            'invoice_id' => Invoice::factory(),
            'master_item_product_id' => $mipId,
            'item_code' => $mipId ? (MasterItemProduct::find($mipId)?->code) : fake()->bothify('ITM-###'),
            'item_name' => $mipId ? (MasterItemProduct::find($mipId)?->name) : fake()->words(2, true),
            'description' => fake()->sentence(),
            'qty' => $qty,
            'unit' => 'UNIT',
            'price' => $price,
            'bruto' => $qty * $price,
            'months' => fake()->randomElement([1, 3, 6, 12]),
        ];
    }
}