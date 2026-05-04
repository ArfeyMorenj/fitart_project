<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('posting_payments', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->date('date');
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->string('acc_code', 20)->nullable();
            $table->string('cdf_code', 20)->nullable();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->decimal('total_invoice', 15, 2)->default(0);
            $table->decimal('debet', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->boolean('print_receipt')->default(false);
            $table->boolean('auto_journal')->default(true);
            $table->boolean('without_stamp')->default(false);
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posting_payments');
    }
};
