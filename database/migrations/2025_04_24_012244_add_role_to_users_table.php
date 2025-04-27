<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(User::ROLE_CUSTOMER)->after('is_admin');
        });

        // Set existing admins to admin role
        DB::table('users')
            ->where('is_admin', 1)
            ->update(['role' => User::ROLE_ADMIN]);
            
        // Set existing non-admins to customer role
        DB::table('users')
            ->where('is_admin', 0)
            ->update(['role' => User::ROLE_CUSTOMER]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
