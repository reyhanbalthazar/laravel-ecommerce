<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function getCategories()
    {
        return Cache::remember('categories', 3600, function () {
            return Category::active()->get();
        });
    }

    public static function getFeaturedProducts()
    {
        return Cache::remember('featured_products', 1800, function () {
            return Product::with(['category', 'images'])
                ->featured()
                ->active()
                ->inStock()
                ->limit(8)
                ->get();
        });
    }

    public static function getNewArrivals()
    {
        return Cache::remember('new_arrivals', 1800, function () {
            return Product::with(['category', 'images'])
                ->active()
                ->inStock()
                ->latest()
                ->limit(8)
                ->get();
        });
    }
}