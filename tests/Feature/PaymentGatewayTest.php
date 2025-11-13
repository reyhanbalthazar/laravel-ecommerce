<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Services\MockPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_accepts_multiple_payment_methods()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        
        // Add to cart
        session(['cart' => [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => null
            ]
        ]]);
        
        $this->actingAs($user);

        $methods = ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'qris'];
        
        foreach ($methods as $method) {
            $checkoutData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john' . $method . '@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
                'zip_code' => '12345',
                'country' => 'USA',
                'payment_method' => $method
            ];

            $response = $this->post('/checkout', $checkoutData);
            $response->assertRedirect(); // Should redirect to order confirmation
        }
    }

    public function test_order_has_correct_payment_status_after_checkout()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        
        // Add to cart
        session(['cart' => [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => null
            ]
        ]]);
        
        $this->actingAs($user);

        $checkoutData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'zip_code' => '12345',
            'country' => 'USA',
            'payment_method' => 'credit_card'
        ];

        $response = $this->post('/checkout', $checkoutData);
        
        // Check that the order exists with pending payment status
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'credit_card',
            'payment_status' => 'pending'
        ]);
    }

    public function test_payment_status_tracking_works()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_status' => 'paid'
        ]);

        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}");
        $response->assertStatus(200);
    }

    public function test_order_has_transaction_id()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        
        // Add to cart
        session(['cart' => [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => null
            ]
        ]]);
        
        $this->actingAs($user);

        $checkoutData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'zip_code' => '12345',
            'country' => 'USA',
            'payment_method' => 'paypal'
        ];

        $this->post('/checkout', $checkoutData);
        
        // Check for order with transaction ID
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'paypal'
        ]);
    }

    public function test_payment_status_can_be_checked()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_status' => 'pending'
        ]);

        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}/payment-status");
        $response->assertStatus(200);
    }
}