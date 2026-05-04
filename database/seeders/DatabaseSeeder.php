<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ChartOfAccountSeeder::class,
            CompanySeeder::class,
            CompanySettingSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            BankSeeder::class,
            InvoiceTypeSeeder::class,
            ItemSeeder::class,
            MasterItemProductSeeder::class,
            ProductGroupSeeder::class,
            TeamMemberSeeder::class,
            ProductSeeder::class,
            InvoiceSeriesSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
