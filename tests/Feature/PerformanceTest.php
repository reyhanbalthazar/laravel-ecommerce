<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_pagination_works()
    {
        // Create enough products to trigger pagination (more than default per page)
        Product::factory(15)->create(); // Assuming default is 12 per page

        $response = $this->get('/products');
        
        $response->assertStatus(200);
        $response->assertViewHas('products');
        
        // Check that we get a paginated result
        $response->assertViewHas('products', function ($products) {
            return $products instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
        });
    }

    public function test_order_pagination_works_in_user_dashboard()
    {
        $user = User::factory()->create();
        Order::factory(15)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get('/orders');
        
        $response->assertStatus(200);
        $response->assertViewHas('orders');
        
        $response->assertViewHas('orders', function ($orders) {
            return $orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
        });
    }

    public function test_admin_order_pagination_works()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Order::factory(15)->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/orders');
        
        $response->assertStatus(200);
        $response->assertViewHas('orders');
        
        $response->assertViewHas('orders', function ($orders) {
            return $orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
        });
    }

    public function test_product_search_pagination_works()
    {
        // Create products with specific names to test search
        Product::factory(3)->create(['name' => 'Laptop Test A']);
        Product::factory(3)->create(['name' => 'Laptop Test B']);
        Product::factory(3)->create(['name' => 'Phone Test']);
        
        $response = $this->get('/products?search=Laptop');
        
        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_eager_loading_works_to_prevent_n_plus_1()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        
        // Create multiple order items to test eager loading
        for ($i = 0; $i < 5; $i++) {
            $product = Product::factory()->create();
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
                'total' => $product->price,
            ]);
        }
        
        $this->actingAs($user);

        // Access order items to make sure eager loading works
        $response = $this->get("/orders/{$order->order_number}");
        
        $response->assertStatus(200);
        $response->assertViewHas('order', function ($order) {
            // The order should have its items loaded to prevent N+1 queries
            $this->assertTrue($order->relationLoaded('items'));
            return count($order->items) > 0;
        });
    }

    public function test_category_pagination_works()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        \App\Models\Category::factory(15)->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/categories');
        
        $response->assertStatus(200);
        $response->assertViewHas('categories');
        
        $response->assertViewHas('categories', function ($categories) {
            return $categories instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
        });
    }

    public function test_wishlist_pagination_works()
    {
        $user = User::factory()->create();
        $wishlist = \App\Models\Wishlist::factory()->create(['user_id' => $user->id]);
        
        // Create multiple products and add them to wishlist
        for ($i = 0; $i < 15; $i++) {
            $product = Product::factory()->create();
            $wishlist->items()->create([
                'product_id' => $product->id
            ]);
        }
        
        $this->actingAs($user);

        $response = $this->get('/wishlist');
        
        $response->assertStatus(200);
        $response->assertViewHas('products');
        
        $response->assertViewHas('products', function ($products) {
            return $products instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
        });
    }
}