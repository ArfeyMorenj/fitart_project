<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_item_products', function (Blueprint $table) {
            $table->string('acc_omzet_np', 20)->nullable()->after('acc_omzet');
        });
    }

    public function down(): void
    {
        Schema::table('master_item_products', function (Blueprint $table) {
            $table->dropColumn('acc_omzet_np');
        });
    }
};
