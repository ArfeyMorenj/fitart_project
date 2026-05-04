<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            [
                'code' => '0000',
                'status' => 'NON GROUP',
                'name' => 'PUBLIC',
                'city' => 'SURABAYA',
                'credit_term_days' => 0,
                'is_active' => true,
            ],
            [
                'code' => '00054',
                'status' => 'NON GROUP',
                'name' => 'FITNESS PLUS INDONESIA',
                'address' => 'Jl. Gatot Subroto No. 50',
                'city' => 'Jakarta',
                'phone' => '08123456789',
                'npwp' => '42.718.163.4-901.000',
                'tax_name' => 'FITNESS PLUS INDONESIA',
                'tax_address' => 'Jl. Gatot Subroto No. 50, Jakarta',
                'credit_term_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => '00049',
                'status' => 'NON GROUP',
                'name' => 'PT INTERNET INI SAJA',
                'city' => 'Surabaya',
                'credit_term_days' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(['code' => $client['code']], $client);
        }
    }
}
