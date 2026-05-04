<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->text('address_invoice')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('city_invoice', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('npkp', 50)->nullable();
            $table->string('tax_name')->nullable();
            $table->string('tax_position')->nullable();
            $table->string('invoice_name')->nullable();
            $table->string('invoice_position')->nullable();
            $table->string('invoice_name_2')->nullable();
            $table->string('invoice_position_2')->nullable();
            $table->string('invoice_tolerance_days', 3)->default('60');
            $table->string('upgrade_days', 3)->default('5');
            $table->text('letterhead_top')->nullable();
            $table->text('letterhead_bottom')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};