<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('invoice_bm_km_date')->nullable()->after('invoice_bm_km');
            $table->text('tax_note')->nullable()->after('tax_number');
            $table->text('invoice_note')->nullable()->after('tax_note');
            $table->string('invoice_mode', 20)->default('NORMAL')->after('invoice_category');
            $table->boolean('without_payment_posting')->default(false)->after('use_old_letterhead');
            $table->boolean('stamp_and_signature')->default(true)->after('without_payment_posting');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_bm_km_date',
                'tax_note',
                'invoice_note',
                'invoice_mode',
                'without_payment_posting',
                'stamp_and_signature',
            ]);
        });
    }
};
