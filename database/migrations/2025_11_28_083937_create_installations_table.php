<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('install_date');
            $table->string('implementor_1')->nullable();
            $table->string('implementor_2')->nullable();
            $table->string('implementor_3')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // optional kalau mau bisa restore
        });
    }

    public function down()
    {
        Schema::dropIfExists('installations');
    }
};
