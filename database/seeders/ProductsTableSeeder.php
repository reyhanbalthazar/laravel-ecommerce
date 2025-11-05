<?php
// database/seeders/ProductsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        // Get all categories
        $categories = Category::all();

        // Create specific featured products
        $featuredProducts = [
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'The most powerful iPhone with titanium design and advanced camera system.',
                'price' => 1199.99,
                'sale_price' => 1099.99,
                'stock' => 25,
                'sku' => 'SKU-APPIP15PM',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories->where('name', 'Smartphones & Tablets')->first()->id,
            ],
            [
                'name' => 'MacBook Pro 16-inch',
                'description' => 'Supercharged by M3 Pro and M3 Max chips for demanding workflows.',
                'price' => 2499.99,
                'stock' => 15,
                'sku' => 'SKU-APPMBP16',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories->where('name', 'Computers & Laptops')->first()->id,
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise cancellation with exceptional sound quality.',
                'price' => 399.99,
                'sale_price' => 349.99,
                'stock' => 30,
                'sku' => 'SKU-SONYWHXM5',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'The ultimate smartphone with AI features and powerful camera.',
                'price' => 1299.99,
                'stock' => 20,
                'sku' => 'SKU-SMSGALS24',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories->where('name', 'Smartphones & Tablets')->first()->id,
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Gaming console with vibrant OLED screen and enhanced audio.',
                'price' => 349.99,
                'sale_price' => 299.99,
                'stock' => 40,
                'sku' => 'SKU-NINDSWOL',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],
        ];

        foreach ($featuredProducts as $productData) {
            Product::create($productData);
        }

        // Create 50 random products using factory
        Product::factory(50)->create();

        // Create some out-of-stock products
        Product::factory(5)->outOfStock()->create();

        // Create some products on sale
        Product::factory(10)->onSale()->create();

        $this->command->info('✅ Products seeded successfully!');
    }
}
