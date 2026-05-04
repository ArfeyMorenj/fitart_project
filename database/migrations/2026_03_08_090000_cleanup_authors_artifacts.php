<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'author_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('author_id');
            });
        }

        if (Schema::hasTable('authors')) {
            Schema::drop('authors');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('authors')) {
            return;
        }

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'author_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('author_id')
                    ->nullable()
                    ->after('author_name')
                    ->constrained('authors')
                    ->nullOnDelete();
            });
        }
    }
};
