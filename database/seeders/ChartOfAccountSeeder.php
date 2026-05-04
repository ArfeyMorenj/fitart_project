<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // Assets
            ['code' => '1', 'name' => 'AKTIVA', 'type' => 'Asset', 'level' => 1, 'is_header' => true],
            ['code' => '11', 'name' => 'AKTIVA LANCAR', 'type' => 'Asset', 'level' => 2, 'parent_code' => '1', 'is_header' => true],
            ['code' => '1101', 'name' => 'Kas/Tunai', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1110', 'name' => 'Kas Bank Umum', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1111', 'name' => 'Deposit In Transit', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1130', 'name' => 'PPN Masukan', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1201', 'name' => 'Bank BCA', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1202', 'name' => 'Bank Mandiri 1', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1203', 'name' => 'Bank Mandiri 2', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1204', 'name' => 'Bank BRI', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1301', 'name' => 'Piutang Usaha', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1511', 'name' => 'Piutang Hardware', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1513', 'name' => 'Piutang License General', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1519', 'name' => 'Piutang Software', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1530', 'name' => 'Piutang Umum', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1532', 'name' => 'Piutang Jasa Implementasi', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1533', 'name' => 'Piutang Lain-lain', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '1535', 'name' => 'Piutang LSA', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],

            // Liabilities
            ['code' => '2', 'name' => 'KEWAJIBAN', 'type' => 'Liability', 'level' => 1, 'is_header' => true],
            ['code' => '21', 'name' => 'KEWAJIBAN LANCAR', 'type' => 'Liability', 'level' => 2, 'parent_code' => '2', 'is_header' => true],
            ['code' => '2101', 'name' => 'Hutang Usaha', 'type' => 'Liability', 'level' => 3, 'parent_code' => '21'],
            ['code' => '2102', 'name' => 'PPN Keluaran', 'type' => 'Liability', 'level' => 3, 'parent_code' => '21'],
            ['code' => '2140', 'name' => 'PPN Keluaran (Legacy)', 'type' => 'Liability', 'level' => 3, 'parent_code' => '21'],
            ['code' => '3213', 'name' => 'Hutang PPN Keluaran', 'type' => 'Liability', 'level' => 3, 'parent_code' => '21'],
            ['code' => '3214', 'name' => 'Hutang PPN Masukan (-)', 'type' => 'Liability', 'level' => 3, 'parent_code' => '21'],

            // Equity
            ['code' => '3', 'name' => 'MODAL', 'type' => 'Equity', 'level' => 1, 'is_header' => true],
            ['code' => '3101', 'name' => 'Modal Disetor', 'type' => 'Equity', 'level' => 2, 'parent_code' => '3'],

            // Revenue
            ['code' => '4', 'name' => 'PENDAPATAN', 'type' => 'Revenue', 'level' => 1, 'is_header' => true],
            ['code' => '5011', 'name' => 'Pendapatan Hardware/Sales', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '5019', 'name' => 'Pendapatan Produk Lain', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '5030', 'name' => 'Pendapatan License Bulanan', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '5031', 'name' => 'Pendapatan License Non-Pajak', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '5032', 'name' => 'Pendapatan Jasa Implementasi', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '5033', 'name' => 'Pendapatan Lain-lain', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '5035', 'name' => 'Pendapatan Maintenance', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],

            // Expenses
            ['code' => '5', 'name' => 'BEBAN', 'type' => 'Expense', 'level' => 1, 'is_header' => true],
            ['code' => '5090', 'name' => 'Beban Diskon Penjualan', 'type' => 'Expense', 'level' => 2, 'parent_code' => '5'],
            ['code' => '5101', 'name' => 'Beban Gaji', 'type' => 'Expense', 'level' => 2, 'parent_code' => '5'],
            ['code' => '5102', 'name' => 'Beban Operasional', 'type' => 'Expense', 'level' => 2, 'parent_code' => '5'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
