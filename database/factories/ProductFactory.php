<?php
// database/factories/ProductFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sale_price' => $this->faker->optional(0.3)->randomFloat(2, 5, 500), // 30% chance of having sale price
            'stock' => $this->faker->numberBetween(0, 100),
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
            'image' => $this->faker->randomElement([
                'products/iphone-15.jpg',
                'products/macbook-pro.jpg',
                'products/airpods.jpg',
                'products/watch.jpg',
                'products/ipad.jpg',
                'products/camera.jpg',
                'products/headphones.jpg',
                'products/keyboard.jpg'
            ]),
            'images' => json_encode([
                'products/product1-1.jpg',
                'products/product1-2.jpg',
                'products/product1-3.jpg'
            ]),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'category_id' => \App\Models\Category::factory(),
        ];
    }

    // State for featured products
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }

    // State for out of stock products
    public function outOfStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => 0,
            ];
        });
    }

    // State for on sale products
    public function onSale()
    {
        return $this->state(function (array $attributes) {
            return [
                'sale_price' => $this->faker->randomFloat(2, 5, $attributes['price'] * 0.8),
            ];
        });
    }
}
