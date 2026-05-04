<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('work_order_id')
                ->nullable()
                ->after('bank_id')
                ->constrained('work_orders')
                ->nullOnDelete();

            // untuk recurring & snapshot: YYYY-MM
            $table->string('period', 7)->nullable()->after('date')->index();

            // mencegah dobel recurring invoice pada WO+periode yang sama
            $table->unique(['work_order_id', 'period'], 'uniq_invoice_wo_period');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('uniq_invoice_wo_period');
            $table->dropConstrainedForeignId('work_order_id');
            $table->dropColumn('period');
        });
    }
};