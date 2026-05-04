<?php

namespace Database\Seeders;

use App\Models\MasterItemProduct;
use App\Models\Item;
use Illuminate\Database\Seeder;

class MasterItemProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::query()->get();

        foreach ($items as $it) {
            MasterItemProduct::updateOrCreate(
                ['code' => 'MIP-' . $it->code],
                [
                    'name' => $it->name,
                    'unit' => 'UNIT',
                    'price' => 0,
                    'acc_omzet' => $it->acc_omzet,
                    'acc_omzet_np' => $it->acc_omzet,
                    'acc_piutang' => $it->acc_piutang,
                    'cdf_omzet' => $it->cdf_omzet,
                    'cdf_piutang' => $it->cdf_piutang,
                    'is_active' => true,
                ]
            );
        }
    }
}
