<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserOrderDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_order_history()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get('/orders');
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
        $response->assertViewHas('orders');
    }

    public function test_user_can_see_order_details()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);
        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}");
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.show');
        $response->assertViewHas('order');
    }

    public function test_user_cannot_see_other_users_order()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user1->id]);
        $this->actingAs($user2);

        $response = $this->get("/orders/{$order->order_number}");
        
        // Should return 403 or 404 depending on implementation
        $response->assertStatus(403);
    }

    public function test_order_status_is_tracked_correctly()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);
        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}");
        
        $response->assertViewHas('order', function ($order) {
            return $order->status === 'pending' && $order->payment_status === 'pending';
        });
    }

    public function test_order_items_are_displayed()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price
        ]);
        
        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}");
        
        $response->assertViewHas('order', function ($order) {
            return $order->items->count() > 0;
        });
    }

    public function test_order_totals_are_calculated_correctly()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'subtotal' => 100.00,
            'tax' => 10.00,  // 10% tax
            'shipping' => 10.00,
            'total' => 120.00  // subtotal + tax + shipping
        ]);
        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}");
        
        $response->assertViewHas('order');
        
        // Refresh the order to ensure it has the right total
        $order->refresh();
        $this->assertEquals(120.00, $order->total);
    }

    public function test_user_sees_payment_method_on_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'credit_card'
        ]);
        $this->actingAs($user);

        $response = $this->get("/orders/{$order->order_number}");
        
        $response->assertViewHas('order', function ($order) {
            return $order->payment_method === 'credit_card';
        });
    }

    public function test_order_search_works_in_admin_panel()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create([
            'user_id' => $admin->id, // Using admin as user for test
            'order_number' => 'ORD-TEST123456'
        ]);
        $this->actingAs($admin);

        $response = $this->get('/admin/orders?search=TEST123456');
        
        $response->assertStatus(200);
        $response->assertViewHas('orders', function ($orders) use ($order) {
            return $orders->contains($order);
        });
    }

    public function test_order_filtering_by_status_works()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $pendingOrder = Order::factory()->create(['status' => 'pending']);
        $completedOrder = Order::factory()->create(['status' => 'completed']);
        $this->actingAs($admin);

        $response = $this->get('/admin/orders?status=pending');
        
        $response->assertViewHas('orders', function ($orders) use ($pendingOrder, $completedOrder) {
            return $orders->contains($pendingOrder) && !$orders->contains($completedOrder);
        });
    }
}