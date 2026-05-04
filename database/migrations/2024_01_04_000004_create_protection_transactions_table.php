<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('protection_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 50);
            $table->string('document_number', 50);
            $table->date('document_date');
            $table->boolean('is_protected')->default(false);
            $table->date('protected_until')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->index(['document_type', 'document_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('protection_transactions');
    }
};
