<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('account_number', 50)->nullable();
            $table->string('account_name')->nullable();
            $table->string('type', 10)->default('M'); // M=Main, S=Secondary
            $table->string('acc_code', 20)->nullable(); // Account Code
            $table->string('cdf_code', 20)->nullable(); // Cash/Discount/Fee Code
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banks');
    }
};