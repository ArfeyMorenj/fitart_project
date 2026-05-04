<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7);
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('client_code', 20);
            $table->string('client_name');
            $table->decimal('beginning_balance', 15, 2)->default(0);
            $table->decimal('netto', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('biaya', 15, 2)->default(0);
            $table->decimal('nota_debet', 15, 2)->default(0);
            $table->decimal('payment', 15, 2)->default(0);
            $table->decimal('ending_balance', 15, 2)->default(0);
            $table->string('status', 20)->default('BELUM LUNAS');
            $table->timestamps();

            $table->index(['period', 'client_id']);
            $table->unique(['period', 'invoice_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('receivables');
    }
};
