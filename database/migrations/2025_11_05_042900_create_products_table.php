<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2); // 10 digits, 2 decimal places
            $table->decimal('sale_price', 10, 2)->nullable(); // For discounted prices
            $table->integer('stock')->default(0);
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->string('image')->nullable();
            $table->json('images')->nullable(); // For multiple product images
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['is_active', 'is_featured']);
            $table->index('price');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
