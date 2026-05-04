<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();

            // Identitas Perusahaan
            $table->string('logo')->nullable();
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('npwp')->nullable();
            $table->date('period_start')->nullable();

            // Pengaturan Lain (Opsional)
            $table->string('acc_ppn_kes')->nullable();
            $table->string('acc_ppn_mas')->nullable();
            $table->string('acc_discount')->nullable();

            // Bank 1
            $table->string('bank1')->nullable();
            $table->string('bank1_sn')->nullable();
            $table->string('bank1_ac')->nullable();

            // Bank 2
            $table->string('bank2')->nullable();
            $table->string('bank2_sn')->nullable();
            $table->string('bank2_ac')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_settings');
    }
};
