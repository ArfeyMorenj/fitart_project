<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run()
    {
        $banks = [
            [
                'code' => 'BM00',
                'name' => 'DEPOSIT IN TRANSIT',
                'type' => 'M',
                'acc_code' => '1111',
                'cdf_code' => '1111',
                'is_active' => true,
            ],
            [
                'code' => 'BM01',
                'name' => 'BANK BCA 5600247257',
                'account_number' => '5600247257',
                'type' => 'M',
                'acc_code' => '1201',
                'cdf_code' => '1201',
                'is_active' => true,
            ],
            [
                'code' => 'BM02',
                'name' => 'BANK MANDIRI 8358201-4',
                'account_number' => '8358201-4',
                'type' => 'M',
                'acc_code' => '1202',
                'cdf_code' => '1202',
                'is_active' => true,
            ],
            [
                'code' => 'BM03',
                'name' => 'BANK BNI 9457201-4',
                'account_number' => '9457201-4',
                'type' => 'M',
                'acc_code' => '1203',
                'cdf_code' => '1203',
                'is_active' => true,
            ],
            [
                'code' => 'BM04',
                'name' => 'BANK BRI 567567790',
                'account_number' => '567567790',
                'type' => 'M',
                'acc_code' => '1204',
                'cdf_code' => '1204',
                'is_active' => true,
            ],
            [
                'code' => 'KH',
                'name' => 'TUNAI/KAS',
                'type' => 'H',
                'acc_code' => '1101',
                'cdf_code' => '1101',
                'is_active' => true,
            ],
        ];

        foreach ($banks as $bank) {
            Bank::updateOrCreate(['code' => $bank['code']], $bank);
        }
    }
}
