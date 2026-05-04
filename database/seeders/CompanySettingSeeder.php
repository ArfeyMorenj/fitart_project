<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'PT.INTEGRASI MEDIA NUSANTARA',
                'address' => 'Puri Regency Business Center Jl Puri Jembangan Baru III/16',
                'city' => 'Surabaya',
                'phone' => '031-8479035',
                'npwp' => '68.900.534.1-609.000',
                'period_start' => '2026-01-01',
                'acc_ppn_kes' => '3213',
                'acc_ppn_mas' => '3214',
                'acc_discount' => '5090',
                'bank1' => 'BANK CENTRAL ASIA (BCA) Hargorejo Surabaya',
                'bank1_sn' => 'PT Integrasi Media Nusantara',
                'bank1_ac' => '5600247257',
                'bank2' => 'BANK MANDIRI',
                'bank2_sn' => 'PT Integrasi Media Nusantara',
                'bank2_ac' => '9358201-4',
            ]
        );
    }
}
