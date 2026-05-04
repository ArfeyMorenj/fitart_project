<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoiceType;
use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $dpp = fake()->numberBetween(100000, 5000000);
        $ppnPercentage = 11;
        $ppn = (int) round($dpp * $ppnPercentage / 100);
        $total = $dpp + $ppn;

        return [
            'number' => 'INV-' . fake()->unique()->numerify('#####'),
            'invoice_bm_km' => null,
            'date' => fake()->date(),
            'due_date' => fake()->optional()->date(),
            'tax_date' => fake()->optional()->date(),
            'tax_number' => null,

            'invoice_type_id' => InvoiceType::query()->inRandomOrder()->value('id') ?? 1,
            'client_id' => Client::factory(),
            'bank_id' => Bank::query()->inRandomOrder()->value('id'),

            'client_address' => fake()->address(),
            'description' => fake()->sentence(),
            'invoice_category' => null,
            'tax_type' => 'T',
            'instance' => 'U',

            'bruto' => $dpp,
            'discount' => 0,
            'dpp' => $dpp,
            'ppn' => $ppn,
            'ppn_percentage' => $ppnPercentage,
            'dp' => 0,
            'other' => 0,
            'total' => $total,

            'include_ppn' => true,
            'use_old_letterhead' => false,
            'auto_journal' => true,
            'pass_protelasi' => false,

            'is_paid' => false,
            'is_posted' => false,
            'posted_date' => null,
        ];
    }
}