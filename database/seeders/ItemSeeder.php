<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            ['code' => '1', 'name' => 'INSTALL', 'acc_omzet' => '5032', 'acc_piutang' => '1532', 'cdf_omzet' => '5032', 'cdf_piutang' => '1532'],
            ['code' => '2', 'name' => 'LICENSE', 'acc_omzet' => '5033', 'acc_piutang' => '1533', 'cdf_omzet' => '5033', 'cdf_piutang' => '1533'],
            ['code' => '3', 'name' => 'MAINTENANCE', 'acc_omzet' => '5035', 'acc_piutang' => '1535', 'cdf_omzet' => '5035', 'cdf_piutang' => '1535'],
            ['code' => '4', 'name' => 'HARDWARE', 'acc_omzet' => '5011', 'acc_piutang' => '1511', 'cdf_omzet' => '5011', 'cdf_piutang' => '1511'],
            ['code' => '5', 'name' => 'DEVELOPMENT SYSTEM', 'acc_omzet' => '5019', 'acc_piutang' => '1519', 'cdf_omzet' => '5019', 'cdf_piutang' => '1519'],
            ['code' => '6', 'name' => 'NETWORK SERVICE', 'acc_omzet' => '5032', 'acc_piutang' => '1532', 'cdf_omzet' => '5032', 'cdf_piutang' => '1532'],
            ['code' => '7', 'name' => 'PRODUK LAINNYA', 'acc_omzet' => '5032', 'acc_piutang' => '1532', 'cdf_omzet' => '5032', 'cdf_piutang' => '1532'],
        ];

        foreach ($items as $item) {
    Item::updateOrCreate(['code' => $item['code']], $item);
}
    }
}
