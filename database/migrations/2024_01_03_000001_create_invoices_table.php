<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->string('invoice_bm_km', 50)->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->date('tax_date')->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->foreignId('invoice_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained()->nullOnDelete();
            $table->text('client_address')->nullable();
            $table->text('description')->nullable();
            $table->string('invoice_category', 50)->nullable();
            $table->string('tax_type', 20)->default('T');
            $table->string('instance', 10)->default('U');
            $table->decimal('bruto', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('dpp', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('ppn_percentage', 5, 2)->default(11);
            $table->decimal('dp', 15, 2)->default(0);
            $table->decimal('other', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->boolean('include_ppn')->default(true);
            $table->boolean('use_old_letterhead')->default(false);
            $table->boolean('auto_journal')->default(true);
            $table->boolean('pass_protelasi')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_posted')->default(false);
            $table->date('posted_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
