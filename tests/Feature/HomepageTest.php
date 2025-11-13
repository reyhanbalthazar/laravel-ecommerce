<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_featured_products()
    {
        // Create some featured products
        $featuredProduct1 = Product::factory()->create([
            'is_featured' => true,
            'is_active' => true,
            'stock' => 10
        ]);
        $featuredProduct2 = Product::factory()->create([
            'is_featured' => true,
            'is_active' => true,
            'stock' => 5
        ]);
        
        // Create a non-featured product
        $nonFeaturedProduct = Product::factory()->create([
            'is_featured' => false,
            'is_active' => true,
            'stock' => 15
        ]);

        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('featuredProducts');
        
        // Check that featured products are in the view
        $response->assertViewHas('featuredProducts', function ($products) use ($featuredProduct1, $featuredProduct2) {
            return $products->contains($featuredProduct1) && $products->contains($featuredProduct2);
        });
    }

    public function test_homepage_shows_new_arrivals()
    {
        // Create products with different creation times
        $product1 = Product::factory()->create(['created_at' => now()->subDays(1)]);
        $product2 = Product::factory()->create(['created_at' => now()]);
        $product3 = Product::factory()->create(['created_at' => now()->subDays(2)]);

        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewHas('newArrivals');
        
        // Check that new arrivals are ordered by creation date
        $response->assertViewHas('newArrivals', function ($products) use ($product1, $product2, $product3) {
            // The newest should be first
            return $products->first()->id === $product2->id;
        });
    }

    public function test_homepage_shows_categories()
    {
        $category1 = Category::factory()->create(['is_active' => true]);
        $category2 = Category::factory()->create(['is_active' => true]);

        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function test_product_listing_shows_all_active_products()
    {
        $activeProduct = Product::factory()->create(['is_active' => true, 'stock' => 5]);
        $inactiveProduct = Product::factory()->create(['is_active' => false, 'stock' => 10]);

        $response = $this->get('/products');
        
        $response->assertStatus(200);
        $response->assertViewHas('products');
        
        // Check that the active product is shown but not the inactive one
        $response->assertViewHas('products', function ($products) use ($activeProduct, $inactiveProduct) {
            return $products->contains($activeProduct) && !$products->contains($inactiveProduct);
        });
    }

    public function test_product_detail_page_works()
    {
        $product = Product::factory()->create(['is_active' => true]);
        $category = Category::factory()->create();
        $relatedProduct = Product::factory()->create([
            'is_active' => true,
            'category_id' => $category->id
        ]);
        
        $response = $this->get("/products/{$product->slug}");
        
        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product', $product);
    }

    public function test_product_search_works()
    {
        $product1 = Product::factory()->create(['name' => 'Laptop', 'is_active' => true]);
        $product2 = Product::factory()->create(['name' => 'Phone', 'is_active' => true]);

        $response = $this->get('/products?search=Laptop');
        
        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($product1, $product2) {
            // Should contain Laptop but not Phone
            return $products->contains($product1) && !$products->contains($product2);
        });
    }

    public function test_product_filtering_by_category_works()
    {
        $category1 = Category::factory()->create(['is_active' => true]);
        $category2 = Category::factory()->create(['is_active' => true]);
        
        $product1 = Product::factory()->create([
            'category_id' => $category1->id,
            'is_active' => true
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category2->id,
            'is_active' => true
        ]);

        $response = $this->get("/category/{$category1->slug}");
        
        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($product1, $product2) {
            // Should contain product from category1 but not category2
            return $products->contains($product1) && !$products->contains($product2);
        });
    }

    public function test_product_sorting_works()
    {
        $productA = Product::factory()->create(['name' => 'Apple', 'price' => 100, 'is_active' => true]);
        $productB = Product::factory()->create(['name' => 'Banana', 'price' => 50, 'is_active' => true]);

        // Test name sorting (A-Z)
        $response = $this->get('/products?sort=name');
        
        $response->assertViewHas('products', function ($products) use ($productA, $productB) {
            $first = $products->first();
            return $first->id === $productA->id; // Apple should come first
        });
        
        // Test price sorting (low to high)
        $response = $this->get('/products?sort=price_low');
        
        $response->assertViewHas('products', function ($products) use ($productB, $productA) {
            $first = $products->first();
            return $first->id === $productB->id; // Lower price (Banana) should come first
        });
    }
}