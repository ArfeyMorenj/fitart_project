<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['code' => '01', 'name' => 'IMAM S ARIFIN', 'position' => 'MANAGER AREA', 'status' => 'STATUS1', 'is_active' => true],
            ['code' => '02', 'name' => 'EDY PRANOTO', 'position' => 'SYSTEM ANALYST', 'status' => 'STATUS1', 'is_active' => true],
            ['code' => '03', 'name' => 'IMANG INDAH AYUNINGRUM', 'position' => 'ADMINISTRASI', 'status' => 'STATUS1', 'is_active' => true],
            ['code' => '04', 'name' => 'ADIT', 'position' => 'PROGRAMMER', 'status' => 'STATUS1', 'is_active' => true],
            ['code' => '05', 'name' => 'FITRI AMALIA', 'position' => 'ADM.KEUANGAN', 'status' => 'STATUS1', 'is_active' => true],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['code' => $member['code']], $member);
        }
    }
}
