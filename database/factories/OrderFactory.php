<?php
// database/factories/OrderFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        $subtotal = $this->faker->randomFloat(2, 50, 1000);
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $this->faker->randomFloat(2, 0, 50);
        $total = $subtotal + $tax + $shipping;

        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->unique()->bothify('??##??##??')),
            'user_id' => \App\Models\User::factory(),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'stripe']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'transaction_id' => 'TXN-' . strtoupper($this->faker->unique()->bothify('??##??##??')),
            'shipping_address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
            'customer_note' => $this->faker->optional()->sentence(),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'payment_status' => 'pending',
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'payment_status' => 'paid',
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ];
        });
    }
}