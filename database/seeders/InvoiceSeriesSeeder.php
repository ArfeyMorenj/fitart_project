<?php

namespace Database\Seeders;

use App\Models\InvoiceSeries;
use Illuminate\Database\Seeder;

class InvoiceSeriesSeeder extends Seeder
{
    public function run(): void
    {
        InvoiceSeries::updateOrCreate(
            [
                'tax_period' => '03-2026',
                'tax_code' => '010',
            ],
            [
                'filled_date' => '2026-03-08',
                'period_start' => '2026-03-01',
                'period_end' => '2026-03-31',
                'sequence' => 1,
                'prefix' => '010',
                'tax_year' => '2026',
                'start_number' => '04239178',
                'end_number' => '04239205',
                'last_number' => '04239178',
                'ppn_percentage' => '11',
                'dpp_percentage' => '1.11',
            ]
        );
    }
}
