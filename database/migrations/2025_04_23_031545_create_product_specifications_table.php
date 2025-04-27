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
        if (!Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('specification_type_id');
                $table->string('value');
                $table->timestamps();

                // Add foreign key constraints separately
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('specification_type_id')->references('id')->on('specification_types')->onDelete('cascade');
                
                // Add a unique constraint to prevent duplicate specifications for the same product
                $table->unique(['product_id', 'specification_type_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
