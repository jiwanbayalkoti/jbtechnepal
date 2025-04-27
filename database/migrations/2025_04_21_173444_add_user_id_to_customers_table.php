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
        // Check if the column exists
        $hasColumn = false;
        try {
            $columns = DB::select('SHOW COLUMNS FROM customers');
            foreach ($columns as $column) {
                if ($column->Field === 'user_id') {
                    $hasColumn = true;
                    break;
                }
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Add the column if it doesn't exist
        if (!$hasColumn) {
            try {
                DB::statement('ALTER TABLE customers ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER id');
                DB::statement('ALTER TABLE customers ADD CONSTRAINT customers_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Handle any errors
                echo $e->getMessage();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE customers DROP FOREIGN KEY customers_user_id_foreign');
            DB::statement('ALTER TABLE customers DROP COLUMN user_id');
        } catch (\Exception $e) {
            // Handle any errors
            echo $e->getMessage();
        }
    }
};
