<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('debit_credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_credit_note_id')->constrained()->cascadeOnDelete();
            $table->string('item_code', 20)->nullable();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('dpp_amount', 15, 2)->default(0);
            $table->decimal('ppn_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('debit_credit_note_items');
    }
};
