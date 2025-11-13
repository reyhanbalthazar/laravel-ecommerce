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

    protected static function booted()
    {
        static::updating(function ($order) {
            // Check if status is changing from pending to processing or completed
            if (($order->getOriginal('status') === 'pending' && 
                 in_array($order->status, ['processing', 'completed'])) ||
                ($order->getOriginal('status') === 'pending' && 
                 $order->getOriginal('payment_status') !== 'paid' && 
                 $order->payment_status === 'paid')) {
                
                // Reduce stock for each order item
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        $product->decrement('stock', $item->quantity);
                    }
                }
            }
            
            // Check if status is changing from processing/completed back to pending/cancelled
            // (Restocking items if order status is reversed)
            if (in_array($order->getOriginal('status'), ['processing', 'completed']) && 
                in_array($order->status, ['pending', 'cancelled'])) {
                
                // Increase stock for each order item
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }
        });
    }

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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'order_number';
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

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' .
            ($badges[$this->status] ?? 'bg-gray-100 text-gray-800') . '">' .
            ucfirst($this->status) . '</span>';
    }

    // Additional scopes
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Payment status methods
    public function checkPaymentStatus()
    {
        if (!$this->transaction_id) {
            return $this->payment_status;
        }

        // In a real implementation, this would call the actual payment provider
        // For mock purposes, we'll return the current status
        return $this->payment_status;
    }

    public function isPaid()
    {
        return in_array($this->payment_status, ['paid']);
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isCancelled()
    {
        return $this->payment_status === 'cancel';
    }

    public function isExpired()
    {
        return $this->payment_status === 'expire';
    }
}
