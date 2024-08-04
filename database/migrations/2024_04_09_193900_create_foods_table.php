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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('food_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount')->nullable();
            $table->enum('discount_type', ['amount', 'percent'])->nullable();
            $table->enum('food_type', ['non-veg', 'veg']);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->string('tag')->nullable();
            $table->integer('max_order_quantity')->nullable();
            $table->string('food_image')->nullable();
            $table->string('variation_name')->nullable();
            $table->enum('select_type', ['s', 'm']);
            $table->string('min')->nullable();
            $table->string('max')->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('subcategories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
