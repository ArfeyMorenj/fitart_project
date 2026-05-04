<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        Company::updateOrCreate(['code' => 'FITART'], [
            'name' => 'PT. Fit Art Technology',
            'address' => 'Jl. Danau Toba blok H',
            'address_invoice' => 'Jl. Danau Toba blok H',
            'city' => 'Kedungkandang Sawojajar',
            'city_invoice' => 'Kota Malang - Jawa Timur',
            'phone' => '0341-XXXXXXX',
            'fax' => '0341-XXXXXXX',
            'email' => 'info@fitart.co.id',
            'website' => 'www.fitart.co.id',
            'npwp' => '65.800.534.1.603.000',
            'invoice_tolerance_days' => '60',
            'upgrade_days' => '5',
        ]);
    }
}
