<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_item_product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_code', 20)->nullable();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('qty')->default(1);
            $table->string('unit', 20)->default('UNIT');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('bruto', 15, 2)->default(0);
            $table->integer('months')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
