<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $groupLsa = ProductGroup::where('code', 'LSA')->first();
        $author = TeamMember::where('code', '03')->first();

        $products = [
            [
                'code' => '01',
                'name' => 'KEUANGAN (SIGMA FAST)',
                'specification' => 'ACCOUNTING DAN KEUANGAN',
                'description' => 'Core accounting module',
                'author_code' => $author?->code,
                'author_name' => $author?->name,
                'compiler' => 'DELPHI 6',
                'year' => '2006',
                'product_group_id' => $groupLsa?->id,
                'is_active' => true,
            ],
            [
                'code' => '03',
                'name' => 'IKLAN KOLOM (SIGMA KOLOM)',
                'specification' => 'ADS MANAGEMENT',
                'description' => 'Iklan kolom module',
                'author_code' => $author?->code,
                'author_name' => $author?->name,
                'compiler' => 'DELPHI 6',
                'year' => '2006',
                'product_group_id' => $groupLsa?->id,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['code' => $product['code']], $product);
        }
    }
}
