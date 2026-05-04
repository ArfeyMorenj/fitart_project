<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('protection_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7)->unique();
            $table->boolean('is_protected')->default(false);
            $table->date('protected_at')->nullable();
            $table->foreignId('protected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('protection_periods');
    }
};
