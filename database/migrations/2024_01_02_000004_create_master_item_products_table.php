<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_item_products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('unit', 20)->default('UNIT');
            $table->decimal('price', 15, 2)->default(0);
            $table->string('acc_omzet', 20)->nullable();
            $table->string('acc_piutang', 20)->nullable();
            $table->string('cdf_omzet', 20)->nullable();
            $table->string('cdf_piutang', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_item_products');
    }
};
