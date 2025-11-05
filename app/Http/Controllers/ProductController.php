<?php
// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::with('category')
            ->featured()
            ->active()
            ->inStock()
            ->limit(8)
            ->get();

        $newArrivals = Product::with('category')
            ->active()
            ->inStock()
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::active()->get();

        return view('home', compact('featuredProducts', 'newArrivals', 'categories'));
    }

    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by category - FIXED
        if ($request->has('category') && !empty($request->category)) {
            $categorySlug = $request->category;
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by price range - FIXED
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', floatval($request->min_price));
        }

        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', floatval($request->max_price));
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
