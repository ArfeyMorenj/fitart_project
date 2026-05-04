<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('licenses', function (Blueprint $table) {
        $table->id();
        $table->string('license_number')->unique();
        $table->foreignId('license_type_id')->constrained('license_types')->cascadeOnDelete();
        $table->foreignId('client_id')->constrained()->cascadeOnDelete();
        $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
        $table->date('license_date');
        $table->date('due_date')->nullable();
        $table->decimal('subtotal', 15, 2)->nullable();
        $table->decimal('discount', 15, 2)->nullable();
        $table->decimal('tax', 15, 2)->nullable();
        $table->decimal('total', 15, 2)->nullable();
        $table->enum('status', ['unpaid', 'partial', 'paid'])->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}


    public function down()
    {
        Schema::dropIfExists('licenses');
    }
};
