<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make subcategory_id nullable
        Schema::table('models', function (Blueprint $table) {
            // Only try to drop if column exists and we can modify it
            if (Schema::hasColumn('models', 'subcategory_id')) {
                // Update to nullable without touching foreign keys
                DB::statement('ALTER TABLE models MODIFY subcategory_id bigint(20) UNSIGNED NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('models', function (Blueprint $table) {
            // Only try to modify if column exists
            if (Schema::hasColumn('models', 'subcategory_id')) {
                // Make it non-nullable again
                DB::statement('ALTER TABLE models MODIFY subcategory_id bigint(20) UNSIGNED NOT NULL');
            }
        });
    }
};
