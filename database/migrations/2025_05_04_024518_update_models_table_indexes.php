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
        Schema::table('models', function (Blueprint $table) {
            // Add indexes to improve search and filter performance
            $table->index('brand_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('is_active');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('models', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['subcategory_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['slug']);
        });
    }
};
