<?php
// database/seeders/CategoriesTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest gadgets and electronic devices',
                'is_active' => true
            ],
            [
                'name' => 'Computers & Laptops',
                'description' => 'Desktop computers, laptops, and accessories',
                'is_active' => true
            ],
            [
                'name' => 'Smartphones & Tablets',
                'description' => 'Mobile phones, tablets, and accessories',
                'is_active' => true
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Home appliances and kitchen equipment',
                'is_active' => true
            ],
            [
                'name' => 'Fashion & Clothing',
                'description' => 'Men and women fashion clothing',
                'is_active' => true
            ],
            [
                'name' => 'Books & Stationery',
                'description' => 'Books, notebooks, and office supplies',
                'is_active' => true
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'is_active' => true
            ],
            [
                'name' => 'Beauty & Health',
                'description' => 'Cosmetics, skincare, and health products',
                'is_active' => true
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Children toys and games for all ages',
                'is_active' => true
            ],
            [
                'name' => 'Automotive',
                'description' => 'Car accessories and automotive tools',
                'is_active' => true
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create additional random categories using factory
        Category::factory(5)->create();

        $this->command->info('✅ Categories seeded successfully!');
    }
}
