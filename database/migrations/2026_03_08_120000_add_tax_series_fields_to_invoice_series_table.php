<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_series', function (Blueprint $table) {
            $table->date('filled_date')->nullable()->after('id');
            $table->string('start_number', 50)->nullable()->after('tax_code');
            $table->string('end_number', 50)->nullable()->after('start_number');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_series', function (Blueprint $table) {
            $table->dropColumn(['filled_date', 'start_number', 'end_number']);
        });
    }
};
