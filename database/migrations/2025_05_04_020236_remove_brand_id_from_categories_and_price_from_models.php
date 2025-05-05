<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove brand_id constraint from categories
        if (Schema::hasColumn('categories', 'brand_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropForeign(['brand_id']);
                $table->dropColumn('brand_id');
            });
        }
        
        // Remove price from models
        if (Schema::hasColumn('models', 'price')) {
            Schema::table('models', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add brand_id back to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
        
        // Add price back to models
        Schema::table('models', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('subcategory_id')->nullable();
        });
    }
};
