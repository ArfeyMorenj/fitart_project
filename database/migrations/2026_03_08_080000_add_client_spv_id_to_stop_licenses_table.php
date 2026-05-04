<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stop_licenses', function (Blueprint $table) {
            $table->foreignId('client_spv_id')
                ->nullable()
                ->after('client_spv')
                ->constrained('team_members')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stop_licenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_spv_id');
        });
    }
};
