<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_series', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('sequence')->default(1);
            $table->string('prefix', 10)->default('001');
            $table->string('tax_period', 10)->nullable();
            $table->string('tax_year', 4)->nullable();
            $table->string('tax_code', 50)->nullable();
            $table->string('last_number', 20)->nullable();
            $table->string('ppn_percentage', 5)->default('11');
            $table->string('dpp_percentage', 5)->default('1.11');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_series');
    }
};