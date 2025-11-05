<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Automatically generate slug from name
    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    // Relationship with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Helper method to get only active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Route binding using slug instead of ID
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
