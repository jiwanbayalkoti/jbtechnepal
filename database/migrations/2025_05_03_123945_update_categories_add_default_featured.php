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
        // First set existing records' is_featured to 0 if null
        DB::table('categories')
            ->whereNull('is_featured')
            ->update(['is_featured' => 0]);

        // Then add the default constraint
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('is_featured')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('is_featured')->change();
        });
    }
};
