<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'payment_method',
        'payment_status',
        'transaction_id',
        'shipping_address',
        'billing_address',
        'customer_note'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Generate unique order number
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods to parse shipping address
    public function getCustomerNameAttribute()
    {
        $lines = explode("\n", $this->shipping_address);
        return $lines[0] ?? 'N/A';
    }

    public function getCustomerEmailAttribute()
    {
        $lines = explode("\n", $this->shipping_address);
        return $lines[1] ?? 'N/A';
    }

    public function getCustomerPhoneAttribute()
    {
        $lines = explode("\n", $this->shipping_address);
        return $lines[2] ?? 'N/A';
    }

    public function getCustomerAddressAttribute()
    {
        $lines = explode("\n", $this->shipping_address);
        return $lines[3] ?? 'N/A';
    }

    public function getCustomerCityStateZipAttribute()
    {
        $lines = explode("\n", $this->shipping_address);
        return $lines[4] ?? 'N/A';
    }

    public function getCustomerCountryAttribute()
    {
        $lines = explode("\n", $this->shipping_address);
        return $lines[5] ?? 'N/A';
    }

    // Original helper methods
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    public function updatePaymentStatus($status)
    {
        $this->update(['payment_status' => $status]);
    }
}
