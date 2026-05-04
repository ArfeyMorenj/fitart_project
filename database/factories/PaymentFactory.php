<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'number' => 'PAY-' . fake()->unique()->numerify('#####'),
            'date' => fake()->date(),

            'invoice_id' => Invoice::factory(),
            'client_id' => function (array $attr) {
                return Invoice::find($attr['invoice_id'])?->client_id;
            },

            'description' => fake()->sentence(),
            'amount' => fake()->numberBetween(500000, 10000000),

            // default false, biar posting lewat endpoint posting
            'is_posted' => false,
        ];
    }
}