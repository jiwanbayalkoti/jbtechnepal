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
        Schema::table('categories', function (Blueprint $table) {
            // Add brand_id as nullable to allow categories without a specific brand
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            
            // Add a brand_featured flag to mark categories that should be featured for a specific brand
            $table->boolean('brand_featured')->default(false)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'brand_featured']);
        });
    }
};
