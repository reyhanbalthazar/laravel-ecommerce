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
        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index(['category_id'], 'idx_products_category_id');
            $table->index(['price'], 'idx_products_price');
            $table->index(['stock'], 'idx_products_stock');
            $table->index(['is_featured'], 'idx_products_is_featured');
            $table->index(['is_active'], 'idx_products_is_active');
            $table->index(['created_at'], 'idx_products_created_at');
        });

        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id'], 'idx_orders_user_id');
            $table->index(['status'], 'idx_orders_status');
            $table->index(['created_at'], 'idx_orders_created_at');
            $table->index(['order_number'], 'idx_orders_order_number');
        });

        // Add indexes to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id'], 'idx_order_items_order_id');
            $table->index(['product_id'], 'idx_order_items_product_id');
        });

        // Add indexes to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['slug'], 'idx_categories_slug');
            $table->index(['name'], 'idx_categories_name');
            $table->index(['is_active'], 'idx_categories_is_active');
        });

        // Add indexes to product_images table
        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id'], 'idx_product_images_product_id');
            $table->index(['is_primary'], 'idx_product_images_is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['idx_products_category_id']);
            $table->dropIndex(['idx_products_price']);
            $table->dropIndex(['idx_products_stock']);
            $table->dropIndex(['idx_products_is_featured']);
            $table->dropIndex(['idx_products_is_active']);
            $table->dropIndex(['idx_products_created_at']);
        });

        // Remove indexes from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['idx_orders_user_id']);
            $table->dropIndex(['idx_orders_status']);
            $table->dropIndex(['idx_orders_created_at']);
            $table->dropIndex(['idx_orders_order_number']);
        });

        // Remove indexes from order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['idx_order_items_order_id']);
            $table->dropIndex(['idx_order_items_product_id']);
        });

        // Remove indexes from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['idx_categories_slug']);
            $table->dropIndex(['idx_categories_name']);
            $table->dropIndex(['idx_categories_is_active']);
        });

        // Remove indexes from product_images table
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['idx_product_images_product_id']);
            $table->dropIndex(['idx_product_images_is_primary']);
        });
    }
};
