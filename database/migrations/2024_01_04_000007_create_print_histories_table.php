<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('print_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('report_type', 50);
            $table->string('document_number', 50)->nullable();
            $table->json('parameters')->nullable();
            $table->timestamp('printed_at');
            $table->timestamps();

            $table->index(['user_id', 'printed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('print_histories');
    }
};
