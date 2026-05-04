<?php

namespace Database\Seeders;

use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class ProductGroupSeeder extends Seeder
{
    public function run()
    {
        $groups = [
            ['code' => 'LSA', 'name' => 'LISENSI APLIKASI SIGMA', 'acc_omzet' => '5033', 'cdf_piutang' => '1535', 'is_active' => true],
            ['code' => '003', 'name' => 'ACCOUNTING DAN KEUANGAN', 'acc_omzet' => '5032', 'cdf_piutang' => '1532', 'is_active' => true],
        ];

        foreach ($groups as $group) {
            ProductGroup::updateOrCreate(['code' => $group['code']], $group);
        }
    }
}
