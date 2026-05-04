<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'PT. Fit Art Technology', 'type' => 'string', 'group' => 'company'],
            ['key' => 'ppn_percentage', 'value' => '11', 'type' => 'integer', 'group' => 'tax'],
            ['key' => 'dpp_percentage', 'value' => '1.11', 'type' => 'string', 'group' => 'tax'],
            ['key' => 'invoice_tolerance_days', 'value' => '60', 'type' => 'integer', 'group' => 'invoice'],
            ['key' => 'auto_journal', 'value' => 'true', 'type' => 'boolean', 'group' => 'accounting'],
            ['key' => 'auto_protection', 'value' => 'true', 'type' => 'boolean', 'group' => 'security'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
