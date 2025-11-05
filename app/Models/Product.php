<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock',
        'sku',
        'image',
        'images',
        'is_featured',
        'is_active',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'images' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);

            // Generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . Str::upper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Accessor for current price (sale price or regular price)
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    // Check if product is on sale
    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    // Check if product is in stock
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    // Route binding using slug instead of ID
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
