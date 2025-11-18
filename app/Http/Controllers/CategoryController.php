<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('categories_with_counts', 3600, function () {
            return Category::active()
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->get();
        });

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        // Cache products by category
        $cacheKey = 'category_products_' . $category->id;
        $products = Cache::remember($cacheKey, 1800, function () use ($category) {
            return Product::where('category_id', $category->id)
                ->active()
                ->with(['category', 'images']) // Add images for better performance
                ->paginate(12);
        });

        return view('categories.show', compact('category', 'products'));
    }
}
