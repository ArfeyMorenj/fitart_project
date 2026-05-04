<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('licenses', function (Blueprint $table) {
        // drop FK dulu
        $table->dropForeign(['license_type_id']);
        
        // lalu drop column
        $table->dropColumn('license_type_id');
    });
}

public function down()
{
    Schema::table('licenses', function (Blueprint $table) {
        $table->unsignedBigInteger('license_type_id')->nullable();
        $table->foreign('license_type_id')->references('id')->on('license_types')->onDelete('cascade');
    });
}


};
