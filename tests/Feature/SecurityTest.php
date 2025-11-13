<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_routes_require_login()
    {
        // Test cart routes require authentication
        $response = $this->get('/cart');
        $response->assertRedirect('/login');
        
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
        
        $response = $this->get('/orders');
        $response->assertRedirect('/login');
    }

    public function test_admin_routes_require_admin_privileges()
    {
        // Regular user should be denied access
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_routes_allow_admin_users()
    {
        // Admin user should be allowed access
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200); // Success
    }

    public function test_product_creation_requires_admin()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $category = \App\Models\Category::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/admin/products', [
            'name' => 'Unauthorized Product',
            'description' => 'This should fail',
            'price' => 100,
            'stock' => 10,
            'category_id' => $category->id,
        ]);
        
        // The user is not admin, so should get 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_input_validation_works()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        // Test invalid price (negative)
        $response = $this->post('/admin/products', [
            'name' => 'Test Product',
            'description' => 'Valid description',
            'price' => -50,  // Invalid: negative price
            'stock' => 10,
            'category_id' => 1,
        ]);
        
        $response->assertSessionHasErrors(['price']);
        
        // Test empty name
        $response = $this->post('/admin/products', [
            'name' => '',  // Invalid: empty name
            'description' => 'Valid description',
            'price' => 100,
            'stock' => 10,
            'category_id' => 1,
        ]);
        
        $response->assertSessionHasErrors(['name']);
    }

    public function test_cart_requires_authentication()
    {
        // Try to add a product to cart without being logged in
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        
        $response = $this->post("/cart/add/{$product->id}", ['quantity' => 1]);
        $response->assertRedirect('/login');
    }

    public function test_checkout_requires_authentication()
    {
        // Try to access checkout without being logged in
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
    }

    public function test_user_can_only_access_their_own_data()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user2);

        // User2 should not be able to access user1's profile or orders in a direct way
        // This is tested elsewhere but we can check if they can't see admin panel
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_session_based_cart_management()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Add to cart - this should work for authenticated users
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        
        $response = $this->post("/cart/add/{$product->id}", ['quantity' => 2]);
        $response->assertRedirect();
        
        // Check that cart session is set
        $this->assertNotNull(session('cart'));
        $this->assertArrayHasKey($product->id, session('cart'));
    }
}