<?php
// database/seeders/OrdersTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        // Get some users and products
        $users = User::where('is_admin', false)->get();
        $products = Product::inStock()->get();

        // Create 30 orders
        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();
            $order = Order::factory()->create([
                'user_id' => $user->id,
            ]);

            // Add 1-5 random products to each order
            $orderProducts = $products->random(rand(1, 5));

            foreach ($orderProducts as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'unit_price' => $product->sale_price ?? $product->price,
                ]);
            }

            // Recalculate order totals based on order items
            $this->recalculateOrderTotals($order);
        }

        // Create some completed orders
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $order = Order::factory()->completed()->create([
                'user_id' => $user->id,
            ]);

            $orderProducts = $products->random(rand(1, 5));

            foreach ($orderProducts as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'unit_price' => $product->sale_price ?? $product->price,
                ]);
            }

            $this->recalculateOrderTotals($order);
        }

        $this->command->info('✅ Orders seeded successfully!');
    }

    private function recalculateOrderTotals($order)
    {
        $subtotal = $order->items->sum('total');
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $subtotal > 100 ? 0 : 15; // Free shipping over $100
        $total = $subtotal + $tax + $shipping;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }
}
